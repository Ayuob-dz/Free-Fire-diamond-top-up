import telebot
import mysql.connector
import logging
import os
from datetime import datetime

# إعدادات البوت
TOKEN = '7423907926:AAHdcrw76o6XH54nvGUk1IO7RGQ6j7BCFYY'
ADMIN_CHAT_ID = 7130722086

bot = telebot.TeleBot(TOKEN)

# إعدادات قاعدة البيانات (عدلها حسب إعداداتك)
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'fire_load',
    'charset': 'utf8mb4'
}

# تخزين حالة المحادثة مؤقتاً
user_state = {}

@bot.message_handler(commands=['start'])
def start(message):
    bot.reply_to(message, "مرحباً! أرسل /deposit لبدء عملية إيداع جديدة.")

@bot.message_handler(commands=['deposit'])
def deposit_start(message):
    chat_id = message.chat.id
    user_state[chat_id] = {'step': 'amount'}
    bot.send_message(chat_id, "أدخل المبلغ الذي تريد إيداعه (بالدولار):")

@bot.message_handler(func=lambda message: True)
def handle_message(message):
    chat_id = message.chat.id
    text = message.text.strip()
    
    if chat_id not in user_state:
        bot.send_message(chat_id, "يرجى البدء بـ /deposit")
        return
    
    step = user_state[chat_id].get('step')
    
    if step == 'amount':
        try:
            amount = float(text)
            if amount <= 0:
                raise ValueError
            user_state[chat_id]['amount'] = amount
            user_state[chat_id]['step'] = 'transaction_id'
            bot.send_message(chat_id, "أدخل رقم العملية (Transaction ID):")
        except:
            bot.send_message(chat_id, "الرجاء إدخال مبلغ صحيح (مثل 10.50)")
    
    elif step == 'transaction_id':
        user_state[chat_id]['transaction_id'] = text
        user_state[chat_id]['step'] = 'screenshot'
        bot.send_message(chat_id, "الرجاء إرسال لقطة شاشة للعملية.")
    
    elif step == 'screenshot':
        if message.photo:
            # الحصول على أكبر صورة
            file_id = message.photo[-1].file_id
            file_info = bot.get_file(file_id)
            downloaded_file = bot.download_file(file_info.file_path)
            
            # حفظ الصورة محلياً
            filename = f"deposit_{chat_id}_{datetime.now().strftime('%Y%m%d_%H%M%S')}.jpg"
            filepath = f"../assets/uploads/{filename}"
            with open(filepath, 'wb') as f:
                f.write(downloaded_file)
            
            # حفظ في قاعدة البيانات
            try:
                conn = mysql.connector.connect(**DB_CONFIG)
                cursor = conn.cursor()
                cursor.execute("""
                    INSERT INTO deposits (user_id, amount, transaction_id, screenshot, status)
                    VALUES (NULL, %s, %s, %s, 'pending')
                """, (user_state[chat_id]['amount'], user_state[chat_id]['transaction_id'], filepath))
                conn.commit()
                cursor.close()
                conn.close()
                
                # إشعار الأدمن
                bot.send_message(ADMIN_CHAT_ID, f"💰 إيداع جديد عبر البوت:\nالمبلغ: {user_state[chat_id]['amount']}\nرقم العملية: {user_state[chat_id]['transaction_id']}\nتم حفظ الصورة.")
                
                bot.send_message(chat_id, "تم استلام طلب الإيداع. سنقوم بمراجعته قريباً.")
                del user_state[chat_id]
            except Exception as e:
                bot.send_message(chat_id, "حدث خطأ في حفظ الطلب. الرجاء المحاولة لاحقاً.")
                logging.error(f"Database error: {e}")
        else:
            bot.send_message(chat_id, "الرجاء إرسال صورة (لقطة شاشة).")

if __name__ == '__main__':
    logging.basicConfig(level=logging.INFO)
    bot.infinity_polling()

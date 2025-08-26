# Flash News Generation and Broadcasting System

## 📌 Overview  
The **Flash News Generation and Broadcasting System** is a web-based application designed to streamline the creation, scheduling, and broadcasting of internal announcements within an organization. It enables authorized users (e.g., ITC, R&D, Manufacturing, Finance departments) to post time-sensitive flash news that appears instantly or at scheduled times across a centralized dashboard.

This project was developed during the internship at **Heavy Vehicles Factory (HVF), Avadi – ITC Section**.

---

## 🚀 Features  
- 🔐 **User Authentication** (Session-based login with role-based access).  
- 📰 **Flash News Creation** (Compose, schedule, and preview news).  
- ⏰ **Date & Time Validation** (Prevents past dates and Sundays).  
- 📅 **Schedule Management** (Cancel or edit scheduled announcements).  
- 👨‍💼 **Admin Approval Panel** (Approve/reject news before publishing).  
- 📊 **Database Storage** (Secure MySQL database for users and news).  
- 📱 **Responsive Design** (Accessible on desktops and mobile devices).  
- 🌍 **Multi-Language Headers** (Supports English, Hindi, Tamil).  

---

## 🛠️ Tech Stack  
- **Frontend:** HTML, CSS, JavaScript (with responsive design).  
- **Backend:** PHP (with MySQLi).  
- **Database:** MySQL.  
- **Server Environment:** XAMPP (Apache, PHP, MySQL).  
- **Additional Tools:** AJAX (for real-time updates), Bootstrap (for responsive UI).  

---

## 📂 Project Structure  
```
/flash-news-system
│── login.php              # User login page
│── dashboard.php          # Flash news dashboard
│── preview_flashnews.php  # Preview submitted news
│── edit_flashnews.php     # Edit existing news
│── get_news_by_date.php   # Fetch news by specific date
│── view_blog.php          # Public news ticker/blog page
│── cancel_news.php        # Cancel scheduled news
│── approve_news.php       # Admin approval panel
│── /db                    # Database schema and scripts
│── /assets                # CSS, JS, and media files
```
---

## ⚙️ Installation & Setup  
1. Clone or download the project files.  
2. Install **XAMPP** and start `Apache` & `MySQL`.  
3. Create a database named `flashnews_db`.  
4. Import the provided SQL schema from the `/db` folder.  
5. Place project files in the `htdocs` folder of XAMPP.  
6. Open browser and go to:  
   ```
   http://localhost/flash-news-system/login.php
   ```

---

## 🎯 Usage  
1. **Login** with authorized credentials.  
2. **Create News** by entering message, date, and time.  
3. **Preview & Schedule** news (cannot select past dates/Sundays).  
4. **Admins** approve or reject news.  
5. **Users** can view, edit, or cancel their scheduled news.  
6. **Flash News Blog Page** displays approved and published news.  

---

## 📌 Future Enhancements  
- 📱 Mobile app version for push notifications.  
- 🔔 Real-time broadcasting with WebSockets.  
- 🌐 Advanced multilingual & text-to-speech integration.  
- 📊 Admin analytics dashboard (read-status, usage insights).  
- 🔗 ERP/Calendar integration for auto-scheduling.  

---

## 👨‍💻 Contributors  
- N Piritha (RMK Engineering College)  
- M Gubendran (Pondicherry University)  
- S R Dikshithaa (Panimalar Engineering College)  
- R Thiyanesh (Sri Sairam Institute of Technology)  
- D Gokul Ranjan (Rajalakshmi Engineering College)  

# 🔍 LaraSEOScan

A Laravel-based on-page SEO analysis tool for developers and marketers to audit websites locally.  
Scan a given URL, and get a detailed report of its:

- ✅ Page title & meta description  
- 🖼️ All images with `alt` attributes  
- 🔗 All links with status codes (working/broken)  
- 🧾 Canonical tag, Robots tag, Headings (H1–H3)  

---

## 📌 Project Purpose

**LaraSEOScan** is a developer-friendly tool built in Laravel that helps you run SEO audits on any web page — locally and privately. It parses core on-page SEO elements and presents a clean report to improve content structure, link health, and image accessibility.

---

## 👤 User Features

- 🔐 Auth system with registration and login
- 📝 Profile with: name, email, phone (with country), company, role
- 📅 Limit of **5 scans per user/day**
- 📜 Personal scan history view
- ♻️ Soft delete support for scans
- 👁️ Users can only view scans they performed

---

## 🚀 Demo

<img src="screenshots/form.png" width="600" alt="URL input form" />
<img src="screenshots/results.png" width="600" alt="SEO results page" />

---

## ⚙️ Setup Instructions

1. **Clone the repo**

```
git clone https://github.com/mayuroza3/LaraSEOScan.git
cd LaraSEOScan
```


2. **Install dependencies**
```
composer install
cp .env.example .env
php artisan key:generate
```

3. **Configure `.env`**
Set up your DB (SQLite recommended for local):
```
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```
Create the database file if it doesn't exist:

4. **Run migrations**
```
php artisan migrate
```

5. **Start the app**
```
php artisan serve
```

Now visit: http://127.0.0.1:8000

---

## 🛠️ Tech Stack

- **Laravel 11**
- **Guzzle HTTP** – for fetching links and web pages
- **Symfony DOMCrawler** – for HTML parsing and DOM inspection
- **Breeze** - for Laravel Auth Scaffolding 
- **Bootstrap 5** – for styling the user interface
- **Postgres/MySQL** – for storing scan results
- **Blade Templating** – for simple and fast rendering

---

## 🤝 How to Contribute

1. Fork the repo  
2. Create a new branch: `feature/my-feature-name`  
3. Make your changes  
4. Submit a Pull Request 🚀

---

## 🧠 TODO / Roadmap Ideas

- Export results as PDF/CSV  
- Bookmark scans for quick access  
- Dashboard analytics for scanned data  
- Scheduled re-scanning  
- Basic authentication or admin dashboard  

---

## 🙌 Author

Created with ❤️ by [Mayur Oza](https://mayuroza.com)  
Follow me on GitHub: [@mayuroza3](https://github.com/mayuroza3)


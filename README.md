# Next Destination - Travel Agency Platform


**Next Destination** is a comprehensive, dynamic Travel Agency Web Application built to handle both client-facing travel bookings and back-office management. It offers a seamless experience for users to browse, search, and book travel packages and hotels, while providing administrators with a powerful dashboard to manage content, availability, and reservations.

---

## 🌟 Key Features

### For Users (Clients)
*   **Dynamic Homepage Slider:** Engaging visual experience with dynamically loaded destination images and videos.
*   **Hotel Booking System:** Search for hotels, check real-time room availability, filter by capacity (adults/children), and complete reservations.
*   **Travel Packages:** Explore detailed travel itineraries, pricing, included/excluded services, and make group or individual bookings.
*   **User Dashboard:** Personal account area to track past and upcoming reservations.
*   **Reviews & Ratings:** Submit reviews and ratings for both hotels and travel packages (subject to admin approval).
*   **Contact & Inquiries:** Directly contact the agency through an integrated messaging system.

### For Administrators
*   **Secure Admin Dashboard:** Centralized hub for managing the entire platform.
*   **Dynamic Slider Management:** Upload, manage, and remove images/videos for the homepage slider directly from the UI.
*   **Hotel & Package CRUD:** Create, read, update, and delete hotel listings and travel packages.
*   **Availability Management:** Manage hotel room types, capacities, and pricing dynamically.
*   **Reservation Management:** View, confirm, cancel, or complete customer reservations.
*   **Review Moderation:** Approve or reject user reviews before they appear on the public site.
*   **Internal Messaging System:** Read and respond to customer inquiries submitted via the contact form.
*   **Agency Settings:** Update agency contact info, social links, and branding details.

---

## 🛠️ Technology Stack

*   **Frontend:** HTML5, CSS3, JavaScript (Vanilla JS & modern ES6+)
*   **Backend:** PHP (Native / PDO for secure database interactions)
*   **Database:** MySQL / MariaDB
*   **Architecture:** Custom MVC-inspired structure separating logic, views, and database interactions.

---

## 🚀 Installation & Setup

Follow these steps to run the project locally on your machine.

### Prerequisites
*   A local server environment like **XAMPP**, **WAMP**, or **MAMP**.
*   PHP 7.4 or higher.
*   MySQL / MariaDB.

### Steps
1.  **Clone the repository:**
    ```bash
    git clone https://github.com/yourusername/next-destination.git
    ```
2.  **Move to local server:**
    Move the cloned project folder into your server's root directory:
    *   For XAMPP: `C:\xampp\htdocs\`
    *   For WAMP: `C:\wamp\www\`
3.  **Database Setup:**
    *   Open phpMyAdmin (usually `http://localhost/phpmyadmin`).
    *   Create a new database named `next_destination`.
    *   Import the provided SQL dump: Go to the `Import` tab and upload `sql/database.sql`.
4.  **Configuration:**
    *   Open `config/db.php` in a text editor.
    *   Update the database credentials if necessary (by default, it uses `root` with no password for XAMPP).
    ```php
    $host = 'localhost';
    $dbname = 'next_destination';
    $db_user = 'root';
    $db_pass = ''; // Leave blank if using default XAMPP
    ```
5.  **Run the Application:**
    *   Open your web browser and navigate to: `http://localhost/next-destination/` *(Adjust the folder name if you renamed it)*.

---

## 🔐 Default Admin Access

To access the Admin Dashboard and test the backend features, use the following default credentials (make sure to change them in production):

*   **Email:** `admin@nextdestination.com`
*   **Password:** `password`

---

## 📁 Project Structure

```text
├── auth/               # Authentication scripts (login, register, logout)
├── config/             # Configuration files (Database connection)
├── css/                # Stylesheets
├── images/             # Uploaded and static images
├── includes/           # Reusable UI components (header, footer, navbar)
├── js/                 # JavaScript files for frontend and admin logic
├── pages/              # Public-facing pages (hotels, packages, contact)
├── pagesbackend/       # Admin dashboard pages and CRUD processing scripts
├── sql/                # Database SQL dump file
├── vids/               # Video assets for the dynamic slider
└── index.php           # Main landing page
```

---

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!
Feel free to check the [issues page](https://github.com/yourusername/next-destination/issues).

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📝 License

This project is open-source and available under the [MIT License](LICENSE).

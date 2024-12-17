# Linear KPI Dashboard

![Linear KPI Dashboard](https://example.com/your-image.png)

## Overview

The **Linear KPI Dashboard** is a web-based application that integrates with the Linear API to provide real-time insights into project ticket metrics. This dashboard allows users to efficiently manage tasks, track progress, and visualize project data in an intuitive interface.

## Features
- **Task Filtering**: Excludes tasks marked as "Done" from the display.
- **Detailed Task View**: Clickable rows that open a sidebar with detailed task information.
- **Responsive Design**: Optimized for both desktop and mobile devices.
- **Dark Mode Support**: Enhanced styling for better visibility in low-light environments.

## Requirements
- PHP 7.4 or higher
- Composer

## Installation
1. **Clone the repository**:
   ```bash
   git clone git@github.com:click-click-io/linear-dashboard.git
   ```
2. **Navigate to the project directory**:
   ```bash
   cd linear-dashboard
   ```
3. **Install dependencies**:
   ```bash
   composer install
   ```
4. **Set up environment variables**:
   Copy `.env.example` to `.env` and configure it according to your environment.
5. **Serve the application**:
   ```bash
   php -S 0.0.0.0:8000 -t public
   ```

## Usage
- Access the dashboard by navigating to `http://localhost:8000` in your web browser.
- Use the sidebar to view detailed information about tasks and manage your workflow.

## Deployment
To deploy the application to a web server:
1. Upload all necessary files to your server using FTP.
2. Ensure the `public` directory is set as the web root.
3. Set correct permissions for `storage` and `bootstrap/cache` directories.

## Contributing
Contributions are welcome! Please submit a pull request or open an issue for any improvements.

## License
This project is licensed under the MIT License. See the LICENSE file for more details.

## Acknowledgments
- [Linear API](https://linear.app/docs) for providing the API for task management.
- [Bootstrap](https://getbootstrap.com/) for the responsive design framework.

---

For any questions or feedback, feel free to reach out!

<p align="right">
  <a href="README-en.md" title="English"><img src="https://flagcdn.com/w40/us.png" width="40" alt="English"></a>
  &nbsp;&nbsp;
  <a href="README.md" title="Português"><img src="https://flagcdn.com/w40/br.png" width="30" alt="Português"></a>
</p>

# 🚀 Presence Validation System v2.0

<p align="center">
  <img src="https://go-skill-icons.vercel.app/api/icons?i=php,apache,mysql,docker,bash,linux,ubuntu,git,githubactions,grafana,ansible" />
</p>

## 📄 Project Overview

The **Presence Validation System** is a robust web application developed to modernize and secure the attendance registration process for academic events at the UNESPAR - Apucarana campus. The project evolved from a simple IP validation script to a complete solution, demonstrating a comprehensive DevOps workflow, from infrastructure as code to real-time monitoring.

The application solves critical problems such as inefficient manual registration, attendance fraud, and the dependency on the IT team for event management.

## 🛠️ Key Pillars and Applied Technologies

| Key Pillar | Applied Tools and Concepts |
|---|---|
| **Containerization** | **Docker and Docker Compose** to package the PHP application, MySQL database, and monitoring stack, ensuring a consistent and isolated environment. |
| **CI/CD (Continuous Integration & Deployment)** | **GitHub Actions** to automate static analysis (`PHPStan`), security auditing (`Composer`), integration tests, and the build and publication of the image to the GitHub Container Registry (GHCR). |
| **Observability** | **PLG Stack (Promtail, Loki, Grafana)** for real-time log collection, storage, and visualization, enabling instant problem diagnosis. |
| **Infrastructure as Code (IaC)** | **Ansible** to automate the configuration of an Ubuntu server from scratch, installing Docker, setting up users, and cloning the project, making the infrastructure fully reproducible. |
| **Security** | Implementation of multiple validation layers (IP, event time, anti-fraud lock), a secure admin panel with login, and `<iframe>` validation to prevent XSS. |

---

## 🏛️ Solution Architecture

The application is orchestrated by Docker Compose and divided into the following services that communicate over an internal network (`app-net`):
- **`web`**: The main container with the PHP application running on an Apache server.
- **`db`**: The MySQL 8.0 database for persisting user, event, and attendance data.
- **`loki`**, **`promtail`**, **`grafana`**: The observability stack for log monitoring.

The production environment is hosted on a dedicated server on campus.
<p align="center">
  <img src="https://github.com/user-attachments/assets/7268088c-2e2b-4425-b211-08b25ca4a288" alt="Homemade server" width="600"/>
</p>

---

## ✨ Key Features (Showcase)

### Secure Admin Panel
A login-protected management panel allows the IT team to manage the entire event lifecycle (create, edit, delete) without needing to intervene in the code.

<p align="center">
  <img src="https://github.com/user-attachments/assets/379872e9-a659-411f-a7e1-4f6101f24c77" alt="Login Screen" width="600"/>
</p>

The administrator can dynamically update the attendance form (e.g., Google Forms) and generate QR Codes for the event with a single click.

<p align="center">
  <img width="1267" height="415" alt="image" src="https://github.com/user-attachments/assets/64f7e05b-f5f5-45a9-b862-6f59ccc12ef3" />
</p>

### Student Validation Flow
To ensure registration integrity, the student undergoes a series of validations:
1.  **IP Validation**: Checks if the access is from the campus Wi-Fi network.
2.  **Time Validation**: Verifies if the registration is being made within the event's time window.
3.  **Anti-Fraud Lock**: Prevents the same student from registering attendance more than once on the same day.

---

## 🔄 DevOps Workflow

### CI/CD Pipeline with GitHub Actions
The pipeline is triggered on every push to the `main` branch and runs a series of checks to ensure code quality and security before publishing the new version of the Docker image.

*Space for CI/CD Pipeline image*
`![CI/CD Pipeline]()`

### Monitoring with Grafana
The observability stack allows for real-time viewing and searching of logs from all containers through a Grafana dashboard, which is essential for debugging during events.

*Space for Grafana image*
`![Grafana Dashboard]()`

### Infrastructure as Code with Ansible
The production server configuration is fully automated with Ansible. The playbook prepares a clean Ubuntu server, installs all dependencies, and deploys the application. For more details, see the **[Ansible Deployment Guide](ANSIBLE_GUIDE.md)**.

*Space for Ansible execution image*
`![Ansible Execution]()`

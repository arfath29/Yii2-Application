# Yii2 Docker Swarm Deployment

This project demonstrates deploying a Yii2 PHP application using Docker Swarm, NGINX reverse proxy, with CI/CD via GitHub Actions and infrastructure automation using Ansible.

## Prerequisites

- AWS EC2 instance (Ubuntu 20.04 LTS)
- Docker Hub account
- GitHub repository

## Setup Instructions

1. Clone this repository
2. Configure your AWS EC2 instance
3. Set up GitHub Secrets:
   - DOCKER_HUB_USERNAME
   - DOCKER_HUB_TOKEN
   - SSH_PRIVATE_KEY
   - SERVER_IP
4. Run the Ansible playbook:
   ```bash
   ansible-playbook -i inventory.ini playbook.yml

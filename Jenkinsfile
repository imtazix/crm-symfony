pipeline {
    agent any

    environment {
        SONAR_TOKEN = credentials('sonarqube-token')
        DOCKERHUB_CREDENTIALS = credentials('dockerhub-credentials')
    }

    stages {
        stage('Cloner le projet') {
            steps {
                git 'https://github.com/imtazix/crm-symfony.git'
            }
        }

        stage('Composer install') {
            steps {
                sh 'docker-compose run --rm php composer install'
            }
        }

        stage('Analyse SonarQube') {
            steps {
                withSonarQubeEnv('MySonarQube') {
                    sh """
                        sonar-scanner \
                        -Dsonar.projectKey=crm-symfony \
                        -Dsonar.sources=src \
                        -Dsonar.host.url=http://localhost:9000 \
                        -Dsonar.login=${SONAR_TOKEN}
                    """
                }
            }
        }

        stage('Build Docker image') {
            steps {
                sh 'docker build -t imtazix/crm-symfony .'
            }
        }

        stage('Push DockerHub') {
            steps {
                sh 'echo $DOCKERHUB_CREDENTIALS_PSW | docker login -u $DOCKERHUB_CREDENTIALS_USR --password-stdin'
                sh 'docker push imtazix/crm-symfony'
            }
        }

        stage('Déploiement avec Ansible') {
            steps {
                sh 'ansible-playbook -i inventory.ini deploy.yml'
            }
        }
    }
}

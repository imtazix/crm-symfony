pipeline {
    agent any

    environment {
        SONARQUBE_ENV = 'SonarQube' // nom configuré dans Jenkins > Manage Jenkins > SonarQube Servers
        DOCKER_IMAGE = 'elmahdi29/crm-symfony'
    }

    stages {
        stage('Checkout') {
            steps {
                git credentialsId: 'github-creds', url: 'https://github.com/imtazix/crm-symfony.git', branch: 'main'
            }
        }

        stage('Composer install') {
            agent {
                docker {
                    image 'composer:2'
                    args '-v /var/run/docker.sock:/var/run/docker.sock'
                }
            }
            steps {
                sh 'composer install'
                sh 'php bin/console cache:clear || true'
            }
        }

        stage('Analyse SonarQube') {
            steps {
                withSonarQubeEnv("${env.SONARQUBE_ENV}") {
                    sh 'sonar-scanner'
                }
            }
        }

        stage('Build Docker image') {
            steps {
                sh 'docker build -t $DOCKER_IMAGE .'
            }
        }

        stage('Push DockerHub') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'dockerhub-credentials', usernameVariable: 'DOCKER_USER', passwordVariable: 'DOCKER_PASS')]) {
                    sh '''
                        echo "$DOCKER_PASS" | docker login -u "$DOCKER_USER" --password-stdin
                        docker push $DOCKER_IMAGE
                    '''
                }
            }
        }
    }
}

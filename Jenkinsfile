pipeline {
    agent any

    environment {
        SONAR_HOST_URL = 'http://localhost:9000'
        SONAR_LOGIN = 'squ_3b87e461253f8d6c08a4a9dbd83ae2f69c1cfe17'
        DOCKER_IMAGE = 'mahdimaadhadh/crm-symfony'
    }

    stages {
        stage('Checkout') {
            steps {
                git credentialsId: 'github-creds', url: 'https://github.com/imtazix/crm-symfony.git', branch: 'main'
                sh 'pwd'
            }
        }

        stage('Composer install') {
            agent {
                docker {
                    image 'composer:2.5'
                    args '-v /var/run/docker.sock:/var/run/docker.sock'
                }
            }
            steps {
                sh 'pwd'
                sh 'composer install'
                sh 'php bin/console cache:clear || true'
            }
        }

        stage('Analyse SonarQube') {
            environment {
                PATH = "/opt/sonar-scanner/bin:$PATH"
            }
            steps {
                sh 'pwd'
                withSonarQubeEnv('SonarQube') {
                    sh '''
                        sonar-scanner \
                        -Dsonar.projectKey=crm-symfony \
                        -Dsonar.sources=. \
                        -Dsonar.host.url=$SONAR_HOST_URL \
                        -Dsonar.login=$SONAR_LOGIN
                    '''
                }
            }
        }

        stage('Build Docker image') {
            steps {
                sh 'pwd'
                retry(3) {
                    timeout(time: 5, unit: 'MINUTES') {
                        sh 'docker build -t $DOCKER_IMAGE .'
                    }
                }
            }
        }

        stage('Push DockerHub') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'dockerhub-credentials', usernameVariable: 'DOCKER_USER', passwordVariable: 'DOCKER_PASS')]) {
                    sh 'pwd'
                    retry(2) {
                        sh '''
                            echo "$DOCKER_PASS" | docker login -u "$DOCKER_USER" --password-stdin
                            docker push $DOCKER_IMAGE
                        '''
                    }
                }
            }
        }

        stage('Déploiement Ansible') {
            steps {
                sh 'pwd'
                sh 'ansible-playbook -i ansible/inventory.ini ansible/deploy.yml'
            }
        }
    }
}

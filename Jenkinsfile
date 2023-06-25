pipeline {

          agent any

          environment {
            DOCKERHUB_CREDENTIALS = credentials('dockerhub_cred')
           }

          
          stages{
            
           stage('build docker image') {
            steps {
                
                sh 'docker build -t camp_symfony .'
                
                  }
            }

             stage('Push image to dockerhub') {

                 steps {

                  sh 'docker tag camp_symfony saria15/camp_symfony'

                  sh 'echo $DOCKERHUB_CREDENTIALS_PSW \
                  | docker login -u $DOCKERHUB_CREDENTIALS_USR \
                  --password-stdin'

                  sh 'docker push saria15/camp_symfony'

                       }
                
                post {

                  always {

                  sh 'docker logout'

                         }

                     }

            }
            
            
            stage('Run docker-compose') {
            steps {
                
                sh 'docker-compose -f docker-compose.yml up -d'
                
                  }
            }
            
            
            
            
      }
}

pipeline {
  agent any
  stages {
    stage('Build') {
      steps {
        sh 'rsync -av --no-p --progress * /var/www/html/product/gofer/ --exclude "config/database.php" --exclude "public/.htaccess" --exclude "public/images/*"'
      }
    }
  }
}
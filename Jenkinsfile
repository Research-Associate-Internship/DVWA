pipeline {
	agent {
		label 'aj-node'
	}
	stages {
		stage ("Git checkout"){
			steps {
				git branch: "master",
					url: "https://github.com/Research-Associate-Internship/DVWA"
				sh "ls"
			}
		}
		
		stage('Snyk Scan') {
			steps{
				snykSecurity organisation: 'suryaviyyapu', snykInstallation: 'SnykJ', snykTokenId: 'Snyk'
			}
		}				
	}
}

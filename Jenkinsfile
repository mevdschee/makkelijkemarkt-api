#!groovy

// Project settings for deployment
String PROJECTNAME = "makkelijkemarkt-api"
String CONTAINERDIR = "."
String PRODUCTION_BRANCH = "master"
String INFRASTRUCTURE = 'secure'
String PLAYBOOK = 'deploy-makkelijkemarkt-api.yml'

// All other data uses variables, no changes needed for static
String CONTAINERNAME = "fixxx/makkelijkemarkt-api:${env.BUILD_NUMBER}"
String DOCKERFILE="Dockerfile"
String BRANCH = "${env.BRANCH_NAME}"


def tryStep(String message, Closure block, Closure tearDown = null) {
    try {
        block();
    }
    catch (Throwable t) {
        slackSend message: "${env.JOB_NAME}: ${message} failure ${env.BUILD_URL}", channel: '#ci-channel-app', color: 'danger'
        throw t;
    }
    finally {
        if (tearDown) {
            tearDown();
        }
    }
}

node {
    stage("Checkout") {
        checkout scm
    }

    stage("Build image") {
        tryStep "build", {
            docker.withRegistry("${DOCKER_REGISTRY_HOST}",'docker_registry_auth') {
                image = docker.build("${CONTAINERNAME}","-f ${DOCKERFILE} ${CONTAINERDIR}")
                image.push()
            }
        }
    }
}

// On master branch, fetch the container, tag with production and latest and deploy to production
if (BRANCH == "${PRODUCTION_BRANCH}") {
    node {
        stage('Deploy to ACC') {
            tryStep "deployment", {
                docker.withRegistry("${DOCKER_REGISTRY_HOST}",'docker_registry_auth') {
                    image.push("acceptance")
                }

                build job: 'Subtask_Openstack_Playbook',
                parameters: [
                    [$class: 'StringParameterValue', name: 'INFRASTRUCTURE', value: '${INFRASTRUCTURE}'],
                    [$class: 'StringParameterValue', name: 'INVENTORY', value: 'acceptance'],
                    [$class: 'StringParameterValue', name: 'PLAYBOOK', value: '${PLAYBOOK}'],
                ]
            }
        }
    }

    stage('Waiting for approval') {
        slackSend channel: '#ci-channel-app', color: 'warning', message: 'Makkelijke markt API is waiting for Production Release - please confirm'
        input "Deploy to Production?"
    }

    node {
        stage('Deploy to PROD') {
            tryStep "deployment", {
                docker.withRegistry("${DOCKER_REGISTRY_HOST}",'docker_registry_auth') {
                    image.push("production")
                    image.push("latest")
                }

                build job: 'Subtask_Openstack_Playbook',
                parameters: [
                    [$class: 'StringParameterValue', name: 'INFRASTRUCTURE', value: '${INFRASTRUCTURE}'],
                    [$class: 'StringParameterValue', name: 'INVENTORY', value: 'production'],
                    [$class: 'StringParameterValue', name: 'PLAYBOOK', value: '${PLAYBOOK}'],
                ]
            }
        }
    }
}

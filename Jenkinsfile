#!groovy

// Project settings for deployment
String PROJECTNAME = "makkelijkemarkt-api"
String CONTAINERDIR = "."
String PRODUCTION_BRANCH = "master"
String PLAYBOOK = 'deploy.yml'

// All other data uses variables, no changes needed for static
String CONTAINERNAME = "fixxx/makkelijkemarkt-api"
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

def retagAndPush(String imageName, String newTag)
{
    def regex = ~"^https?://"
    def dockerReg = "${DOCKER_REGISTRY_HOST}" - regex
    sh "docker tag ${dockerReg}/${imageName}:${env.BUILD_NUMBER} ${dockerReg}/${imageName}:${newTag}"
    sh "docker push ${dockerReg}/${imageName}:${newTag}"
}

node {
    stage("Checkout") {
        checkout scm
    }

    stage("Build image") {
        tryStep "build", {
            docker.withRegistry("${DOCKER_REGISTRY_HOST}",'docker_registry_auth') {
                image = docker.build("${CONTAINERNAME}:${env.BUILD_NUMBER}","-f ${DOCKERFILE} ${CONTAINERDIR}")
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
                    docker.image("${CONTAINERNAME}:${env.BUILD_NUMBER}").pull()
                    // The Image.push() function ignores the docker registry prefix of the image name,
                    // which means that we cannot re-tag an image that was built in a different stage (on a different node).
                    // Resort to manual tagging to allow build and tag steps to run on different Jenkins slaves.
                    retagAndPush("${CONTAINERNAME}", "acceptance")
                }

                build job: 'Subtask_Openstack_Playbook',
                parameters: [
                    [$class: 'StringParameterValue', name: 'INVENTORY', value: 'acceptance'],
                    [$class: 'StringParameterValue', name: 'PLAYBOOK', value: "${PLAYBOOK}"],
                    [$class: 'StringParameterValue', name: 'PLAYBOOKPARAMS', value: "-e cmdb_id=app_${PROJECTNAME}"],
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
                    docker.image("${CONTAINERNAME}:${env.BUILD_NUMBER}").pull()
                    retagAndPush("${CONTAINERNAME}", "production")
                    retagAndPush("${CONTAINERNAME}", "latest")
                }

                build job: 'Subtask_Openstack_Playbook',
                parameters: [
                    [$class: 'StringParameterValue', name: 'INVENTORY', value: 'production'],
                    [$class: 'StringParameterValue', name: 'PLAYBOOK', value: "${PLAYBOOK}"],
                    [$class: 'StringParameterValue', name: 'PLAYBOOKPARAMS', value: "-e cmdb_id=app_${PROJECTNAME}"],
                ]
            }
        }
    }
}

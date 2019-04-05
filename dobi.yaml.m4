meta:
    project: 'm4ProjectName()'
    default: build

image=dockerImage:
    image: m4ReleaseImage()
    tags: ['m4ReleaseImageTag()']
    dockerfile: Dockerfile
    args:
        commit: '{git.short-sha}'

job=openShell:
    use: dockerImage
    interactive: true
    command: sh

alias=shell:
    tasks: [openShell]
    annotations:
        description: "Start an interactive shell"

alias=build:
    tasks: ['dockerImage:build']
    annotations:
        description: "Build the image"

alias=push:
    tasks: ['dockerImage:push']
    annotations:
        description: "Build and push the image"

.PHONY:  kubernetes/app.production.yaml, Dockerfile, unit_test, release, dobi.yaml, push_tag
unit_test:
	@echo "+++ Unit tests +++"

override projectRootDir = ./
override projectVersionFile = VERSION
override projectVersion = $(shell head -n1 $(projectVersionFile))
override gitOriginUrl = $(shell git config --get remote.origin.url)
override projectName=frontend
override projectRegistry=$(REGISTRY)
override projectPath=$(REPOSITORY_PATH)
override releaseImage = $(REGISTRY)/$(REPOSITORY_PATH)/app-$(RUNTIME):$(projectVersion)

override containerBasePath=$(REGISTRY)/$(REPOSITORY_PATH)/app-$(RUNTIME)
override dobiDeps = kubernetes/app.production.yaml dobi.yaml Dockerfile push_tag docker_login
dobiTargets = shell build push autoclean

# helper macros
override getImage = $(firstword $(subst :, ,$1))
override getImageTag = $(or $(word 2,$(subst :, ,$1)),$(value 2))
override printRow = @printf "%+22s = %-s\n" $1 $2

override M4_OPTS = \
	--define m4ProjectName=$(projectName) \
	--define m4ProjectVersion=$(projectVersion) \
	--define m4GitOriginUrl=$(gitOriginUrl) \
	--define m4ReleaseImage=$(call getImage, $(releaseImage)) \
	--define m4ReleaseImageTag=$(call getImageTag, $(releaseImage),latest) \
	--define m4ContainerBasePath=$(containerBasePath)


kubernetes/app.production.yaml: kubernetes/app.production.m4.yaml
	@echo "\n + + + Build Kubernetes app yml + + + "
	@m4 $(M4_OPTS) kubernetes/app.production.m4.yaml > kubernetes/app.production.yaml

Dockerfile: Dockerfile.m4
	@echo "\n + + + Build Dockerfile + + + "
	@m4 $(M4_OPTS) Dockerfile.m4 > Dockerfile

dobi.yaml: dobi.yaml.m4 $(projectVersionFile) Makefile
	@m4 $(M4_OPTS) dobi.yaml.m4 > dobi.yaml

$(dobiTargets): $(dobiDeps)
	@dobi $@

clean: | autoclean
	-@rm -rf .dobi dobi.yaml Dockerfile kubernetes/app.production.yaml

args=$(filter-out $@,$(MAKECMDGOALS))
VERSION_TAG=$(args)
release:
	$(if $(args),,$(error: set project version string, when calling this task))
	@echo "\n + + + Set next version: $(VERSION_TAG) + + + "
	@echo $(VERSION_TAG) > ./VERSION

push_tag:
	@echo "\n + + + Push tags to repository + + + "
	@git add .
	@git commit -m "Changes for next release $(VERSION_TAG)"
	@git tag -s $(VERSION_TAG) -m "Next release $(VERSION_TAG)"
	@git push --tags origin master


docker_login:
	@echo "\n + + + Login into registry: $(REGISTRY) with user $(REGISTRY_USER):$(REGISTRY_PASSWORD) +  +  + "
	@docker login -p$(REGISTRY_PASSWORD) -u$(REGISTRY_USER) $(REGISTRY)

docker_logout:
	@echo "\n + + + Logout from registry: $(REGISTRY) +  +  + "
	@docker logout $(REGISTRY)

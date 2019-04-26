.PHONY:  kubernetes/app.production.yaml, Dockerfile, unit_test, release, dobi.yaml, push_tag
unit_test:
	@echo "+++ Unit tests +++"

override projectRootDir = ./
override projectVersionFile = VERSION
override projectVersion = $(VERSION_TAG)
override gitOriginUrl = $(shell git config --get remote.origin.url)
override projectName=frontend
override projectRegistry=$(REGISTRY)
override projectPath=$(REPOSITORY_PATH)
override baseContainerPath=registry.gitlab.com/froscon/php-track-web
override releaseImage = $(REGISTRY)/$(REPOSITORY_PATH)/app-$(RUNTIME):$(projectVersion)

override containerBasePath=$(REGISTRY)/$(REPOSITORY_PATH)/app-$(RUNTIME)
override dobiDeps = kubernetes/app.production.yaml dobi.yaml Dockerfile docker_login
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
	--define m4ContainerBasePath=$(containerBasePath) \
	--define m4BaseContainerPath=$(baseContainerPath)


kubernetes/app.production.yaml: kubernetes/app.production.m4.yaml $(projectVersionFile) Makefile
	@echo "\n + + + Build Kubernetes app yml + + + "
	@m4 $(M4_OPTS) kubernetes/app.production.m4.yaml > kubernetes/app.production.yaml

Dockerfile: Dockerfile.m4
	@echo "\n + + + Build Dockerfile + + + "
	@m4 $(M4_OPTS) Dockerfile.m4 > Dockerfile

dobi.yaml: dobi.yaml.m4 $(projectVersionFile) Makefile
	@m4 $(M4_OPTS) dobi.yaml.m4 > dobi.yaml

$(dobiTargets): $(dobiDeps)
	$(if $(VERSION_TAG),,$(error: set project version string on VERSION_TAG, when calling this task))
	@echo $(VERSION_TAG) > ./VERSION
	@echo " + + + Do it with version $(VERSION_TAG) + + + "
	@dobi $@

clean: | autoclean
	-@rm -rf .dobi dobi.yaml Dockerfile kubernetes/app.production.yaml

release:
	$(if $(VERSION_TAG),,$(error: set project version string on VERSION_TAG, when calling this task))
	@echo "\n + + + Set next version: $(VERSION_TAG) + + + "
	@echo $(VERSION_TAG) > ./VERSION
	@make kubernetes/app.production.yaml
	@echo "\n + + + Push tags to repository + + + "
	@git add .
	@git commit -m "Changes for next release $(VERSION_TAG)"
	@git tag -s $(VERSION_TAG) -m "Next release $(VERSION_TAG)"
	@git push --tags gitlab master


docker_login:
	@echo "\n + + + Login into registry: $(REGISTRY) +  +  + "
	@docker login -p$(REGISTRY_PASSWORD) -u$(REGISTRY_USER) $(REGISTRY)

docker_logout:
	@echo "\n + + + Logout from registry: $(REGISTRY) +  +  + "
	@docker logout $(REGISTRY)

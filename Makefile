.PHONY:  kubernetes/app.production.yaml, Dockerfile, unit_test, before_release, release,
unit_test:
	@echo "+++ Unit tests +++"

override projectRootDir = ./
override projectVersionFile = $(projectRootDir)/VERSION
override projectVersion = $(shell head -n1 $(projectVersionFile))
override gitOriginUrl = $(shell git config --get remote.origin.url)
override projectName=frontend
override projectRegistry=$(REGISTRY)
override projectPath=$(REPOSITORY_PATH)
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
	--define m4ContainerBasePath=$(containerBasePath)


kubernetes/app.production.yaml: $(projectRootDir)/kubernetes/app.production.m4.yaml
	@echo @m4 "$(M4_OPTS) $(projectRootDir)/kubernetes/app.production.m4.yaml > $(projectRootDir)/kubernetes/app.production.yaml"
	@m4 $(M4_OPTS) $(projectRootDir)/kubernetes/app.production.m4.yaml > $(projectRootDir)/kubernetes/app.production.yaml

Dockerfile: Dockerfile.m4
	@echo @m4 "$(M4_OPTS) $(projectRootDir)/Dockerfile.m4 > $(projectRootDir)/Dockerfile"
	@m4 $(M4_OPTS) $(projectRootDir)/Dockerfile.m4 > $(projectRootDir)/Dockerfile

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
	@echo "Release next version: $(VERSION_TAG)"
	@echo $(VERSION_TAG) > ./VERSION
	@make kubernetes/app.production.yaml
	@make Dockerfile
	@git add .
	@git commit -m "Changes for next release $(VERSION_TAG)"
	@git tag -s $(VERSION_TAG) -m "Next release $(VERSION_TAG)"
	@git push --tags origin master


docker_login:
	@echo "+ + + Login into registry: $(REGISTRY) with user $(REGISTRY_USER):$(REGISTRY_PASSWORD) +  +  + "
	@docker login -p$(REGISTRY_PASSWORD) -u$(REGISTRY_USER) $(REGISTRY)

docker_logout:
	@echo "+ + + Logout from registry: $(REGISTRY) +  +  + "
	@docker logout $(REGISTRY)

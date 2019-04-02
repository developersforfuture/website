include Makefile.docker

.PHONY: unit_test, before_release, release
unit_test:
	@echo "+++ Unit tests +++"
override projectRootDir = .
override projectVersionFile = $(projectRootDir)/VERSION
override projectVersion = $(shell head -n1 $(projectVersionFile))
override gitOriginUrl = $(shell git config --get remote.origin.url)
override projectRegistry=$(REGISTRY)
override projectPath=$(REPOSITORY_PATH)
override baseImage = $(REGISTRY)/$(REPOSITORY_PATH)/app-$(RUNTIME):$(projectVersion)
override containerBasePath=$(REGISTRY)/$(REPOSITORY_PATH)/app-$(RUNTIME)

# helper macros
override getImage = $(firstword $(subst :, ,$1))
override getImageTag = $(or $(word 2,$(subst :, ,$1)),$(value 2))
override printRow = @printf "%+22s = %-s\n" $1 $2

override M4_OPTS = \
	--define m4ProjectName=$(projectName) \
	--define m4ProjectVersion=$(projectVersion) \
	--define m4GitOriginUrl=$(gitOriginUrl) \
	--define m4BaseImage=$(call getImage, $(baseImage)) \
	--define m4BaseImageTag=$(call getImageTag, $(baseImage)) \
	--define m4ContainerBasePath=$(containerBasePath)

kubernetes/app.production.yaml: kubernetes/app.production.m4.yaml
	@echo @m4 "$(M4_OPTS) $(projectRootDir)/kubernetes/app.production.m4.yaml > $(projectRootDir)/kubernetes/app.production.yaml"

Dockerfile: Dockerfile.m4
	@echo @m4 "$(M4_OPTS) $(projectRootDir)/Dockerfile.m4 > $(projectRootDir)/Dockerfile"

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


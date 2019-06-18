# Developers For Future - Website

This is the repository for the webpage of [DevelopersForFuture](https://developersforfuture.org)

Currently it is a Symfony application with some help of Symfony CMF. To run the app locally run a:

```
git clone git@github.com:developersforfuture/website.git
cd website/app/src
composer install
bin/console server:run
```

## Build and Deployment

At the end we do manually build a application wide image by running:

```
cp .env.dist .env # and fill the vars with your values
source .env
# set the version you'd like to tag in ./VERSION
# make
# make push
# that currently does not work properly

# set the same version in kubernetes/app.production.yml
git add .
git commit -m 'set new version on image' 
git tag -s <version-tag-you-want> -m '<message-you-want'
git push --tags origin master

make docker_login # the reason you should have added your token to the .env file
docker build -t registry.gitlab.com/developersforfuture/registry/app-production:<version-tag-you-want> .
docker push registry.gitlab.com/developersforfuture/registry/app-production:<version-tag-you-want>
```

### Run the application

There are several ways to run the app.

#### 1 Use Docker

```bash
docker run -it -d registry.gitlab.com/developersforfuture/registry/app-production:<version-tag-you-want> # add the arguments you like to move it to the host port you like to
```
#### 2 PHP Server by a Symfony command

```bash
cd app/src
composer install
./bin/console server:run
```

### 3 Using Kubernetes

```bash
kubectl -n <your-namespace> apply -f https://raw.githubusercontent.com/developersforfuture/website/master/kubernetes/app.production.yaml
```

## Development

To run it in dev mode it would be the easiest way if you choose the second solution.

### Assets by Webpack

As the assets are handled by webpack, you should run its whatcher when working with Sass or Typescript files. So:

```bash
cd app/src
npm run watch
```
Doing so, you will always get the changes.

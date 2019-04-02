# Developers For Future - Website

This is the repository for the webpage of [DevelopersForFuture](https://developersforfuture.org)

Currently it is a Symfony application with some help of Symfony CMF. To run the app locally run a:

```
git clone git@github.com:developersforfuture/website.git
cd website/aapp/src
composer install
bin/console server:run
```

At the end we do manually buidl a application wide image by running:
```
docker build -t -f https://github.com/developersforfuture/website/blob/master/kubernetes/app.production.yaml .
docker push https://github.com/developersforfuture/website/blob/master/kubernetes/app.production.yaml
```
tbd.

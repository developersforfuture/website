apiVersion: v1
clusters:
  - cluster:
      certificate-authority-data: m4KubeCertificateAuthorityData()
      server: https://z2jlrhd8lw.bki1.metakube.syseleven.de:31051
    name: gitlab
contexts:
  - context:
      cluster: gitlab
      namespace: default
      user: gitlab-admin
    name: default
current-context: default
kind: Config
preferences: {}
users:
  - name: gitlab-admin
    user:
      token: m4KubeToken()

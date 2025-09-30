set -e  # stop en cas d'erreur
echo "===> Reconstruction de l'image Docker pour dev."
docker-compose -f docker-compose.yml build --no-cache app

echo "===> Redémarrage des containers dev."
docker-compose -f docker-compose.yml up -d

echo "===> Nettoyage des anciennes images inutilisées."
docker image prune -f

echo "✅ Déploiement dev terminé !"
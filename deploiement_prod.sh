
set -e  # stop en cas d'erreur
echo "===> Reconstruction de l'image Docker pour la prod."
docker-compose -f docker-compose.prod.yml build --no-cache app-prod

echo "===> Redémarrage des containers prod."
docker-compose -f docker-compose.prod.yml up -d

echo "===> Nettoyage des anciennes images inutilisées."
docker image prune -f

echo "✅ Déploiement prod terminé !"

Pour lancer le projet : 

```docker compose up -d```

Renommer le fichier .env.exemple a la racine du projet en .env
```composer install```
```php bin/console doctrine:migrations:migrate```
```symfony serve```

Si tout s'est bien passe le serveur est disponible sur l'adresse [https](https://localhost:8001/)

Ensuite, se rendre sur l'onglet s'inscrire -> creer un utilisateur.
Une fois fait, se rendre dans la base de donnée et ajouter ["ROLE_ADMIN"] à notre user.

A partir de là, vous pouvez creer des categories/sous-categories/produits ainsi que gérer les utilisateurs inscrits. Vous pourrez les promouvoir en tant que moderateur ou les expulser.

Vous pouvez creer d'autres utilisateurs qui vont acheter votre produit et utiliser stripe.
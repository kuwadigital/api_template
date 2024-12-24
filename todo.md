# tous les attributs qui sont des Enum doivent etre validé (bref ajouter toutes les validations necessaires, JSON, Unique, Not Blanc ....)
# empecher que les Roles symfony soient donnees par les requte post, put, patch, donc: s'assurer que les proprietes critiques soient editables uniquement par le root, il existe une solution sur api-platform, ajouter les normlisations groups de toutes les entités de security
# s'inspirer de la commande app-db-import et app-db-export de template-api pour creer pour nous
# ajouter une route qui met l'API en maintenance mode et une route pour reactiver (peut on prevenir quelques heures jours minutes avant ??)
# ajouter les tests unitaires
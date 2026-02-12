# Guide de Test Rapide - Activation par Email

## Tester l'API avec curl (Windows PowerShell)

### 1. Créer un compte (et recevoir l'email d'activation)

```powershell
curl -X POST http://localhost:8000/api/auth/signup `
  -H "Content-Type: application/json" `
  -d '{
    \"email\": \"test@example.com\",
    \"password\": \"Test123!\",
    \"firstName\": \"Jean\",
    \"lastName\": \"Test\",
    \"siret\": \"12345678901234\",
    \"companyName\": \"Test Company\"
  }'
```

**Résultat attendu**: Email envoyé avec un code à 6 chiffres

---

### 2. Vérifier le code d'activation

```powershell
curl -X POST http://localhost:8000/api/auth/verify-email `
  -H "Content-Type: application/json" `
  -d '{
    \"email\": \"test@example.com\",
    \"code\": \"123456\"
  }'
```

Remplacez `123456` par le code reçu par email.

**Résultat attendu**: Compte activé + token JWT

---

### 3. Tester la connexion sans vérification (devrait échouer)

Créez un nouveau compte mais n'entrez pas le code:

```powershell
curl -X POST http://localhost:8000/api/auth/login `
  -H "Content-Type: application/json" `
  -d '{
    \"email\": \"test@example.com\",
    \"password\": \"Test123!\"
  }'
```

**Résultat attendu**: Erreur 403 "Compte non vérifié"

---

### 4. Renvoyer l'email d'activation

```powershell
curl -X POST http://localhost:8000/api/auth/resend-activation `
  -H "Content-Type: application/json" `
  -d '{
    \"email\": \"test@example.com\"
  }'
```

**Résultat attendu**: Nouvel email envoyé avec un nouveau code

---

## Vérifier directement dans la base de données

```sql
-- Voir tous les comptes avec leur statut de vérification
SELECT email, first_name, last_name, verification_code, is_verified 
FROM user_profiles 
ORDER BY created_at DESC;

-- Activer manuellement un compte (pour test)
UPDATE user_profiles 
SET is_verified = 1, verification_code = NULL 
WHERE email = 'test@example.com';
```

---

## Checklist de Test

- [ ] Créer un compte via l'API
- [ ] Vérifier la réception de l'email
- [ ] Le code est-il visible et correct dans l'email ?
- [ ] Vérifier le code via l'API
- [ ] Le compte est-il activé dans la base de données ?
- [ ] Essayer de se connecter avant vérification (devrait échouer)
- [ ] Essayer de se connecter après vérification (devrait réussir)
- [ ] Tester le renvoi d'email
- [ ] Vérifier que l'ancien code ne fonctionne plus

---

## Démarrer le serveur PHP (si pas déjà démarré)

```powershell
cd c:\xamppv2\htdocs\rappel\api
php -S localhost:8000
```

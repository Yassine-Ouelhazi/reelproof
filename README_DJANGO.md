# ReelProof — Django rewrite scaffold

This repository includes a Django port of the original ReelProof PHP application, translated into Django MVT with Django templates, static assets, and the same UI theme.

## What is included

- Django apps wired for the features from the PHP app: `home`, `products`, `brands`, `reviews`, `accounts`
- Root route and landing pages ported: home, explore, search, category
- Product and brand pages ported with theme styling and card-based UI
- Auth templates and flows ported: login, register, profile settings
- Review templates and review card components ported
- Static assets imported and wired: `static/css/main.css`, `static/js/main.js`

## Quick setup

After creating and activating a virtual environment:

```bash
python -m venv .venv
.venv\Scripts\activate
pip install -r reelproof_django/requirements.txt
cd reelproof_django
python manage.py migrate
python manage.py createsuperuser
python manage.py runserver
```

Also see the condensed root quick start: [QUICK_START.md](QUICK_START.md)

## Key routes

- `/` → home product feed
- `/explore/` → explore page
- `/search/` → search results
- `/category/<slug>/` → category feed
- `/brands/` → brand list
- `/brands/<slug>/` → brand detail
- `/brands/dashboard/` → brand dashboard
- `/brands/products/create/` → create product
- `/products/` → product list
- `/products/<slug>/` → product detail
- `/reviews/` → review list
- `/accounts/login/` → login
- `/accounts/register/` → register
- `/accounts/settings/` → profile settings

## Notes

- The current scaffold is complete for ported views and templates, but functional validation requires running Django in a local environment.
- Update `reelproof_django/reelproof_django/settings.py` if you want to switch from SQLite to another database.

## Git workflow

Create a branch and push:

```bash
git checkout -b feature/django-rewrite
git add .
git commit -m "Add Django rewrite scaffold with templates and routes"
git push -u origin feature/django-rewrite
```

Rollback safely:

```bash
git log --oneline
git revert <commit-hash>
git push
```

Force reset (only if needed):

```bash
git reset --hard <commit-hash>
git push --force
```

## Next steps

- Run the Django server locally and verify pages
- Wire any remaining UI interactions or pagination details
- Add fixtures or seed data for easier testing

If you want, I can also update the main `README.md` to include a link to this Django rewrite documentation.

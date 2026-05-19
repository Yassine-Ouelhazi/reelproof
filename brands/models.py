from django.db import models
from django.conf import settings
from django.utils.text import slugify


class Brand(models.Model):
    user = models.ForeignKey(settings.AUTH_USER_MODEL,
                             null=True, blank=True, on_delete=models.SET_NULL,
                             related_name='brands')
    name = models.CharField(max_length=120)
    slug = models.SlugField(max_length=120, unique=True)
    description = models.TextField(blank=True, null=True)
    logo = models.CharField(
        max_length=255, default='uploads/avatars/default.png')
    website = models.CharField(max_length=255, blank=True, default='')
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self):
        return self.name

    def save(self, *args, **kwargs):
        if not self.slug:
            base = slugify(self.name)
            slug = base
            i = 1
            while Brand.objects.filter(slug=slug).exists():
                slug = f"{base}-{i}"
                i += 1
            self.slug = slug
        super().save(*args, **kwargs)

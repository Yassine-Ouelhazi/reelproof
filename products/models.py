from django.db import models
from django.utils.text import slugify
from brands.models import Brand


class Category(models.Model):
    name = models.CharField(max_length=80)
    slug = models.SlugField(max_length=80, unique=True)
    icon = models.CharField(max_length=50, default='📦')
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self):
        return self.name


class Product(models.Model):
    STATUS_CHOICES = [('active', 'Active'), ('inactive', 'Inactive')]

    brand = models.ForeignKey(
        Brand, on_delete=models.CASCADE, related_name='products')
    category = models.ForeignKey(
        Category, null=True, blank=True, on_delete=models.SET_NULL)
    name = models.CharField(max_length=120)
    slug = models.SlugField(max_length=120, unique=True)
    description = models.TextField(blank=True, null=True)
    image = models.CharField(max_length=255, blank=True)
    price = models.DecimalField(max_digits=10, decimal_places=2, default=0.00)
    status = models.CharField(
        max_length=20, choices=STATUS_CHOICES, default='active')
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self):
        return self.name

    def save(self, *args, **kwargs):
        if not self.slug:
            base = slugify(self.name)
            slug = base
            i = 1
            while Product.objects.filter(slug=slug).exists():
                slug = f"{base}-{i}"
                i += 1
            self.slug = slug
        super().save(*args, **kwargs)

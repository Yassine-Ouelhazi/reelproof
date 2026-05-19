from django import forms
from products.models import Product


class BrandProductForm(forms.ModelForm):
    class Meta:
        model = Product
        fields = ['name', 'category', 'description',
                  'image', 'price', 'status']

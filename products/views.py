from django.db import models
from django.views.generic import ListView, DetailView
from django.db.models import Count, Avg
from django.shortcuts import get_object_or_404

from .models import Product, Category


class ProductListView(ListView):
    model = Product
    template_name = 'products/index.html'
    context_object_name = 'products'

    def get_queryset(self):
        return Product.objects.filter(status='active').annotate(
            review_count=Count('review'),
            avg_rating=Avg('review__rating')
        ).order_by('-review_count')

    def get_context_data(self, **kwargs):
        ctx = super().get_context_data(**kwargs)
        ctx['categories'] = Category.objects.all()
        ctx['category'] = None
        return ctx


class ProductDetailView(DetailView):
    model = Product
    template_name = 'products/show.html'
    slug_field = 'slug'
    slug_url_kwarg = 'slug'

    def get_context_data(self, **kwargs):
        ctx = super().get_context_data(**kwargs)
        reviews = self.object.review_set.filter(status='published')
        ctx['reviews'] = reviews
        ctx['review_count'] = reviews.count()
        ctx['avg_rating'] = reviews.aggregate(
            Avg('rating'))['rating__avg'] or 0
        return ctx


class ProductSearchView(ListView):
    model = Product
    template_name = 'products/index.html'
    context_object_name = 'products'

    def get_queryset(self):
        query = self.request.GET.get('q', '')
        qs = Product.objects.filter(status='active')
        if query:
            qs = qs.filter(
                models.Q(name__icontains=query) |
                models.Q(description__icontains=query)
            ).distinct()
        return qs.annotate(
            review_count=Count('review'),
            avg_rating=Avg('review__rating')
        ).order_by('-review_count')

    def get_context_data(self, **kwargs):
        ctx = super().get_context_data(**kwargs)
        ctx['categories'] = Category.objects.all()
        ctx['category'] = None
        return ctx


class CategoryProductListView(ListView):
    model = Product
    template_name = 'products/index.html'
    context_object_name = 'products'

    def get_queryset(self):
        self.category = get_object_or_404(Category, slug=self.kwargs['slug'])
        return Product.objects.filter(status='active', category=self.category).annotate(
            review_count=Count('review'),
            avg_rating=Avg('review__rating')
        ).order_by('-review_count')

    def get_context_data(self, **kwargs):
        ctx = super().get_context_data(**kwargs)
        ctx['categories'] = Category.objects.all()
        ctx['category'] = self.category
        return ctx

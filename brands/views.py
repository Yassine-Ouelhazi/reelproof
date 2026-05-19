from django.db import models
from django.views.generic import ListView, DetailView, CreateView, TemplateView
from django.contrib.auth.mixins import LoginRequiredMixin
from django.shortcuts import get_object_or_404
from django.urls import reverse_lazy
from django.db.models import Count, Avg

from .models import Brand
from .forms import BrandProductForm
from products.models import Product


class BrandListView(ListView):
    model = Brand
    template_name = 'brands/index.html'
    context_object_name = 'brands'

    def get_queryset(self):
        return Brand.objects.annotate(
            product_count=Count('product'),
            review_count=Count('product__review', distinct=True),
            avg_rating=Avg('product__review__rating')
        ).order_by('-review_count')


class BrandDetailView(DetailView):
    model = Brand
    template_name = 'brands/show.html'
    slug_field = 'slug'
    slug_url_kwarg = 'slug'

    def get_context_data(self, **kwargs):
        ctx = super().get_context_data(**kwargs)
        ctx['products'] = Product.objects.filter(
            brand=self.object, status='active'
        ).annotate(
            review_count=Count('review', filter=models.Q(
                review__status='published'))
        )
        return ctx


class BrandDashboardView(LoginRequiredMixin, TemplateView):
    template_name = 'brands/dashboard.html'

    def get_context_data(self, **kwargs):
        context = super().get_context_data(**kwargs)
        brand = Brand.objects.filter(user=self.request.user).first()
        if brand is None:
            return {'brand': None, 'products': []}
        context['brand'] = brand
        context['products'] = brand.products.all()
        return context


class BrandProductCreateView(LoginRequiredMixin, CreateView):
    model = Product
    form_class = BrandProductForm
    template_name = 'brands/create_product.html'
    success_url = reverse_lazy('brand-dashboard')

    def get_brand(self):
        return Brand.objects.filter(user=self.request.user).first()

    def dispatch(self, request, *args, **kwargs):
        if self.get_brand() is None:
            return self.handle_no_permission()
        return super().dispatch(request, *args, **kwargs)

    def form_valid(self, form):
        form.instance.brand = self.get_brand()
        return super().form_valid(form)

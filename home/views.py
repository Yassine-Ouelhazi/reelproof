from django.contrib.auth.mixins import LoginRequiredMixin
from django.shortcuts import get_object_or_404, redirect
from django.views.generic import TemplateView
from django.core.paginator import Paginator
from django.db.models import Count, Avg, Q

from reviews.models import Review
from products.models import Category, Product
from brands.models import Brand


PAGE_SIZE = 12


class FeedMixin:
    sort_map = {
        'latest': ['-created_at'],
        'trending': ['-views', '-upvotes'],
        'top': ['-upvotes'],
    }

    def get_sort_order(self):
        return self.sort_map.get(self.request.GET.get('sort', 'latest'), ['-created_at'])

    def get_page(self):
        try:
            return int(self.request.GET.get('page', 1))
        except ValueError:
            return 1

    def paginate(self, queryset):
        paginator = Paginator(queryset, PAGE_SIZE)
        return paginator.get_page(self.get_page())

    def get_feed(self, category=None):
        qs = Review.objects.filter(status='published').select_related(
            'user', 'product', 'product__brand')
        if category is not None:
            qs = qs.filter(product__category=category)
        return qs.order_by(*self.get_sort_order())

    def get_categories(self):
        return Category.objects.annotate(
            product_count=Count('product', filter=Q(product__status='active'))
        ).order_by('-product_count')

    def get_top_brands(self):
        return Brand.objects.annotate(
            product_count=Count('products', filter=Q(
                products__status='active')),
            review_count=Count('products__review', filter=Q(
                products__review__status='published'), distinct=True),
            avg_rating=Avg('products__review__rating', filter=Q(
                products__review__status='published'))
        ).order_by('-review_count')[:6]


class HomeIndexView(FeedMixin, TemplateView):
    template_name = 'home/index.html'

    def get_context_data(self, **kwargs):
        ctx = super().get_context_data(**kwargs)
        ctx['feed'] = self.paginate(self.get_feed())
        ctx['categories'] = self.get_categories()
        ctx['top_brands'] = self.get_top_brands()
        ctx['sort'] = self.request.GET.get('sort', 'latest')
        return ctx


class ExploreView(FeedMixin, TemplateView):
    template_name = 'home/explore.html'

    def get_context_data(self, **kwargs):
        ctx = super().get_context_data(**kwargs)
        category_slug = self.request.GET.get('category')
        category = None
        if category_slug:
            category = get_object_or_404(Category, slug=category_slug)
        ctx['feed'] = self.paginate(self.get_feed(category))
        ctx['categories'] = self.get_categories()
        ctx['sort'] = self.request.GET.get('sort', 'trending')
        ctx['category'] = category
        return ctx


class SearchView(TemplateView):
    template_name = 'home/search.html'

    def get_context_data(self, **kwargs):
        ctx = super().get_context_data(**kwargs)
        query = self.request.GET.get('q', '')
        ctx['query'] = query
        ctx['results'] = {'products': [], 'reviews': []}
        if len(query) >= 2:
            ctx['results']['products'] = Product.objects.filter(
                status='active'
            ).filter(
                Q(name__icontains=query) | Q(description__icontains=query)
            ).annotate(
                review_count=Count('review', filter=Q(
                    review__status='published'))
            )[:20]
            ctx['results']['reviews'] = Review.objects.filter(
                status='published'
            ).filter(
                Q(title__icontains=query)
                | Q(description__icontains=query)
                | Q(product__name__icontains=query)
                | Q(product__brand__name__icontains=query)
            ).select_related('product', 'user', 'product__brand').order_by('-upvotes')[:20]
        return ctx


class CategoryView(FeedMixin, TemplateView):
    template_name = 'home/category.html'

    def get_context_data(self, **kwargs):
        ctx = super().get_context_data(**kwargs)
        category = get_object_or_404(Category, slug=self.kwargs['slug'])
        ctx['category'] = category
        ctx['feed'] = self.paginate(self.get_feed(category))
        return ctx


class DashboardView(LoginRequiredMixin, TemplateView):
    template_name = 'home/dashboard.html'

    def dispatch(self, request, *args, **kwargs):
        if request.user.role == 'brand':
            return redirect('brand-dashboard')
        return super().dispatch(request, *args, **kwargs)

    def get_context_data(self, **kwargs):
        ctx = super().get_context_data(**kwargs)
        ctx['reviews'] = []
        if self.request.user.role == 'reviewer':
            ctx['reviews'] = Review.objects.filter(
                user=self.request.user
            ).select_related('product', 'product__brand').order_by('-created_at')
        return ctx

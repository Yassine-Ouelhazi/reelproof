from django.views.generic import ListView, DetailView, CreateView, UpdateView, View
from django.contrib.auth.mixins import LoginRequiredMixin
from django.shortcuts import get_object_or_404
from django.http import JsonResponse
from django.urls import reverse_lazy
from django.db import transaction

from .models import Review, ReviewVote


class ReviewListView(ListView):
    model = Review
    template_name = 'reviews/index.html'
    context_object_name = 'reviews'

    def get_queryset(self):
        return Review.objects.filter(status='published').select_related('product', 'user')


class ReviewDetailView(DetailView):
    model = Review
    template_name = 'reviews/show.html'


class ReviewCreateView(LoginRequiredMixin, CreateView):
    model = Review
    fields = ['product', 'title', 'description',
              'video_path', 'thumbnail', 'rating']
    template_name = 'reviews/create.html'
    success_url = reverse_lazy('review-list')

    def form_valid(self, form):
        form.instance.user = self.request.user
        return super().form_valid(form)


class ReviewUpdateView(LoginRequiredMixin, UpdateView):
    model = Review
    fields = ['product', 'title', 'description',
              'video_path', 'thumbnail', 'rating']
    template_name = 'reviews/edit.html'

    def get_queryset(self):
        return Review.objects.filter(user=self.request.user)

    def get_success_url(self):
        return reverse_lazy('review-detail', kwargs={'pk': self.object.pk})


class ReviewVoteView(LoginRequiredMixin, View):
    def post(self, request, pk):
        review = get_object_or_404(Review, pk=pk)
        vote_type = request.POST.get('type')
        if vote_type not in ('up', 'down'):
            return JsonResponse({'error': 'invalid vote'}, status=400)

        with transaction.atomic():
            existing = ReviewVote.objects.filter(
                review=review, user=request.user).first()
            if existing:
                if existing.type == vote_type:
                    # remove
                    existing.delete()
                    if vote_type == 'up':
                        review.upvotes = max(review.upvotes - 1, 0)
                    else:
                        review.downvotes = max(review.downvotes - 1, 0)
                    review.save()
                    return JsonResponse({'action': 'removed', 'type': vote_type})
                else:
                    # switch
                    prev = existing.type
                    existing.type = vote_type
                    existing.save()
                    if vote_type == 'up':
                        review.upvotes += 1
                        review.downvotes = max(review.downvotes - 1, 0)
                    else:
                        review.downvotes += 1
                        review.upvotes = max(review.upvotes - 1, 0)
                    review.save()
                    return JsonResponse({'action': 'switched', 'type': vote_type})

            # add new
            ReviewVote.objects.create(
                review=review, user=request.user, type=vote_type)
            if vote_type == 'up':
                review.upvotes += 1
            else:
                review.downvotes += 1
            review.save()
            return JsonResponse({'action': 'added', 'type': vote_type})

from django.contrib.auth.mixins import UserPassesTestMixin


class BrandRequiredMixin(UserPassesTestMixin):
    def test_func(self):
        user = self.request.user
        return user.is_authenticated and getattr(user, 'role', None) == 'brand'

</main>

<footer class="footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <span class="brand-icon">▶</span>
            <span>Reel<strong>Proof</strong></span>
            <p>Real video reviews. Real people. Real trust.</p>
        </div>
        <div class="footer-links">
            <div>
                <h4>Discover</h4>
                <a href="<?= url('explore') ?>">Explore</a>
                <a href="<?= url('brands') ?>">Brands</a>
                <a href="<?= url('products') ?>">Products</a>
            </div>
            <div>
                <h4>Join</h4>
                <a href="<?= url('register') ?>">Become a Reviewer</a>
                <a href="<?= url('register') ?>">Register as Brand</a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© <?= date('Y') ?> ReelProof. Built with purpose.</p>
    </div>
</footer>

<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>

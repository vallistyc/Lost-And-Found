</main>

<footer class="bg-white border-t border-slate-200 mt-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-6 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <span class="w-7 h-7 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                </span>
                <span class="text-sm font-bold text-slate-900">LostFound KAMPUS</span>
            </div>
            <p class="text-sm text-slate-600">
                <a href="#" class="hover:text-blue-600">Tentang Kami</a> •
                <a href="#" class="hover:text-blue-600">Syarat &amp; Ketentuan</a> •
                <a href="#" class="hover:text-blue-600">Panduan Pengguna</a> •
                <a href="#" class="hover:text-blue-600">Kontak Bantuan</a>
            </p>
        </div>
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-6 text-sm text-slate-400">
            <p>© <?= date('Y') ?> LostFound KAMPUS. Hak Cipta Dilindungi.</p>
            <p>Mengembalikan barang berharga ke tangan yang tepat.</p>
        </div>
    </div>
</footer>

<script src="assets/js/main.js"></script>

<script>
    const btnMenu = document.getElementById('btn-menu');
    const menuMobile = document.getElementById('menu-mobile');
    btnMenu?.addEventListener('click', () => {
        const buka = menuMobile.classList.toggle('hidden') === false;
        btnMenu.setAttribute('aria-expanded', buka);
    });
</script>

</body>
</html>
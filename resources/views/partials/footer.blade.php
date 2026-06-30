<footer class="mt-16 border-t border-[#dce5f1] bg-white">
    <div class="dm-shell grid gap-8 py-10 md:grid-cols-[1.3fr_1fr_1fr]">
        <div>
            <x-diagnomed.logo />
            <p class="mt-4 max-w-xl text-sm leading-6 text-slate-600">DiagnoMed membantu masyarakat memperoleh informasi rekomendasi obat untuk gejala penyakit ringan secara edukatif, terstruktur, dan mudah dipahami.</p>
            <div class="mt-5 rounded-[8px] bg-amber-50 p-4 text-xs leading-5 text-amber-800">
                Sistem bukan pengganti diagnosis dokter. Jika gejala berat atau tidak membaik dalam 3 x 24 jam, konsultasikan ke tenaga kesehatan.
            </div>
        </div>

        <div>
            <h3 class="text-sm font-bold text-slate-950">Informasi Kontak</h3>
            <div class="mt-4 grid gap-3 text-sm text-slate-600">
                <span class="font-bold text-slate-900">{{ $contact['pharmacy_name'] }}</span>
                <a href="https://wa.me/{{ $contact['whatsapp'] }}" class="font-semibold text-[#2385dd]">WA: {{ $contact['phone'] }}</a>
                <span>Instagram: @{{ $contact['instagram'] }}</span>
                <span>Facebook: {{ $contact['facebook'] }}</span>
                <span>Jam buka: {{ $contact['hours'] }}</span>
                <span>Lokasi: {{ $contact['address'] }}</span>
                <a href="{{ $contact['maps_url'] }}" class="font-semibold text-[#2385dd]" target="_blank" rel="noopener">Buka Google Maps</a>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-bold text-slate-950">Mini Maps</h3>
            <div class="mt-4 overflow-hidden rounded-[8px] border border-[#dce5f1] bg-[#f8fbff]">
                <iframe title="Mini Maps" class="h-40 w-full" loading="lazy" src="https://maps.google.com/maps?q=Apotek%20Bhakti%20Medika%20Farma%20Jl.%20Moch.%20Toha%20No.77%20Bandung&t=&z=16&ie=UTF8&iwloc=&output=embed"></iframe>
            </div>
            <p class="mt-2 text-xs leading-5 text-slate-500">{{ $contact['maps_plus_code'] }}</p>
        </div>
    </div>
</footer>

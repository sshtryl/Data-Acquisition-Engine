export function initCompanyLocation() {
    const form = document.querySelector('form[data-location-form]');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const input = document.getElementById('query');
        const resultBox = document.querySelector('[data-location-result]');
        const query = input.value;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ query }),
            });

            const data = await response.json();

            if (!response.ok) {
                resultBox.innerHTML = `<p class="text-red-500">${data.error ?? 'Terjadi kesalahan.'}</p>`;
                return;
            }

            renderLocationResult(resultBox, data);
        } catch (err) {
            resultBox.innerHTML = `<p class="text-red-500">Gagal menghubungi server.</p>`;
        }
    });
}

function renderLocationResult(container, data) {
    const address = data.address && Object.keys(data.address).length
        ? `{ ${Object.entries(data.address).map(([key, value]) => `${key}: ${value}`).join(', ')} }`
        : '-';

    container.innerHTML = `
        <div class="space-y-2">
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Display Name:</span>
                <span class="break-all">${data.display_name ?? '-'}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Latitude:</span>
                <span class="break-all">${data.latitude ?? '-'}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Longitude:</span>
                <span class="break-all">${data.longitude ?? '-'}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Importance:</span>
                <span class="break-all">${data.importance ?? '-'}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">OSM Type:</span>
                <span class="break-all">${data.osm_type ?? '-'}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Address:</span>
                <span class="break-all">${address}</span>
            </div>
        </div>
    `;
}
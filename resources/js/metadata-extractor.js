export function initMetadataExtractor() {
    const form = document.querySelector('form[data-metadata-form]');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const input = document.getElementById('search');
        const resultBox = document.querySelector('[data-metadata-result]');
        const url = input.value;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ url }),
            });

            const data = await response.json();

            if (!response.ok) {
                resultBox.innerHTML = `<p class="text-red-500">${data.error ?? 'Terjadi kesalahan.'}</p>`;
                return;
            }

            renderResult(resultBox, data);
        } catch (err) {
            resultBox.innerHTML = `<p class="text-red-500">url tidak valid</p>`;
        }
    });
}

function renderResult(container, data) {
    const email = data.email?.length ? `[${data.email.join(', ')}]` : '-';
    const phone = data.phone?.length ? `[${data.phone.join(', ')}]` : '-';
    const socialMedia = data.social_media?.length ? `[${data.social_media.join(', ')}]` : '-';

    const openGraph = data.open_graph
        ? `{ title: ${data.open_graph.title ?? '-'}, description: ${data.open_graph.description ?? '-'}, image: ${data.open_graph.image ?? '-'} }`
        : '-';

    container.innerHTML = `
        <div class="space-y-2">
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">URL:</span>
                <span class="break-all">${data.url ?? '-'}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Title:</span>
                <span class="break-all">${data.title ?? '-'}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Description:</span>
                <span class="break-all">${data.description ?? '-'}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Canonical:</span>
                <span class="break-all">${data.canonical ?? '-'}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Favicon:</span>
                <span class="break-all">${data.favicon ?? '-'}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Email:</span>
                <span class="break-all">${email}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Phone:</span>
                <span class="break-all">${phone}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Social Media:</span>
                <span class="break-all">${socialMedia}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Open Graph:</span>
                <span class="break-all">${openGraph}</span>
            </div>
        </div>
    `;
}
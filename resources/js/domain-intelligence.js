export function initDomainIntelligence() {
    const form = document.querySelector("form[data-domain-form]");
    if (!form) return;

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const input = document.getElementById("domain");
        const resultBox = document.querySelector("[data-domain-result]");
        let domain = input.value.trim();

        // Normalize input: strip protocol and path, keep hostname only
        domain = domain.replace(/^https?:\/\//i, "");
        domain = domain.split("/")[0];

        // Client-side validation to give faster feedback
        const domainPattern = /^([a-z0-9-]+\.)+[a-z]{2,}$/i;
        if (!domainPattern.test(domain)) {
            resultBox.innerHTML = `<p class="text-red-500">Format domain tidak valid. Contoh: example.com</p>`;
            return;
        }

        try {
            const response = await fetch(form.action, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                },
                body: JSON.stringify({ domain }),
            });

            const data = await response.json();

            if (!response.ok) {
                resultBox.innerHTML = `<p class="text-red-500">${data.error ?? "Terjadi kesalahan."}</p>`;
                return;
            }

            renderDomainResult(resultBox, data);
        } catch (err) {
            resultBox.innerHTML = `<p class="text-red-500">Gagal menghubungi server.</p>`;
        }
    });
}

function renderDomainResult(container, data) {
    const status = data.status?.length ? `[${data.status.join(", ")}]` : "-";
    const nameservers = data.nameservers?.length
        ? `[${data.nameservers.join(", ")}]`
        : "-";

    container.innerHTML = `
        <div class="space-y-2">
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Domain:</span>
                <span class="break-all">${data.domain ?? "-"}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Registrar:</span>
                <span class="break-all">${data.registrar ?? "-"}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Registered At:</span>
                <span class="break-all">${data.registered_at ?? "-"}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Expired At:</span>
                <span class="break-all">${data.expired_at ?? "-"}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Last Updated:</span>
                <span class="break-all">${data.last_updated ?? "-"}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Status:</span>
                <span class="break-all">${status}</span>
            </div>
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">Nameservers:</span>
                <span class="break-all">${nameservers}</span>
            </div>
        </div>
    `;
}

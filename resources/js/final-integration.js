export function initFinalIntegration() {
    const form = document.querySelector("form[data-company-form]");
    if (!form) return;

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const input = document.getElementById("domain");
        const resultBox = document.querySelector("[data-company-result]");
        const domain = input.value;

        try {
            const response = await fetch(
                `${form.action}?domain=${encodeURIComponent(domain)}`,
                {
                    method: "GET",
                    headers: {
                        Accept: "application/json",
                    },
                },
            );

            const data = await response.json();

            if (!response.ok) {
                resultBox.innerHTML = `<p class="text-red-500">${data.error ?? "Terjadi kesalahan."}</p>`;
                return;
            }

            renderCompanyResult(resultBox, data);
        } catch (err) {
            resultBox.innerHTML = `<p class="text-red-500">Gagal menghubungi server.</p>`;
        }
    });
}

function renderCompanyResult(container, data) {
    container.innerHTML = `
        <div class="space-y-4">
            ${renderSection("Website", data.website)}
            ${renderSection("Domain", data.domain)}
            ${renderSection("Location", data.location)}
        </div>
    `;
}

function renderSection(title, sectionData) {
    if (!sectionData) {
        return `
            <div>
                <h3 class="font-semibold text-heading mb-1">${title}</h3>
                <div class="ml-4 text-body">-</div>
            </div>
        `;
    }

    const rows = Object.entries(sectionData)
        .map(([key, value]) => {
            let displayValue;
            if (Array.isArray(value)) {
                displayValue = value.length ? `[${value.join(", ")}]` : "-";
            } else if (value && typeof value === "object") {
                displayValue = `{ ${Object.entries(value)
                    .map(([k, v]) => `${k}: ${v}`)
                    .join(", ")} }`;
            } else {
                displayValue = value ?? "-";
            }

            return `
            <div class="flex gap-2">
                <span class="font-medium text-heading shrink-0">${key}:</span>
                <span class="break-all">${displayValue}</span>
            </div>
        `;
        })
        .join("");

    return `
        <div>
            <h3 class="font-semibold text-heading mb-1">${title}</h3>
            <div class="ml-4 space-y-1">${rows}</div>
        </div>
    `;
}

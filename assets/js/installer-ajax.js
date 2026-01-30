/**
 * Keystone CMS Installer
 * ----------------------
 * Alle installer-gerelateerde JavaScript zit in deze namespace.
 *
 * Verantwoordelijkheden:
 * - AJAX calls naar /installer/run
 * - UI updates per stap
 * - Navigatie tussen stappen (loadStep)
 *
 * Backend bepaalt:
 * - of een stap mag slagen
 * - welke fouten er zijn
 *
 * Frontend bepaalt:
 * - wanneer een stap wordt uitgevoerd
 * - hoe resultaten getoond worden
 */

window.Installer = {

    /**
     * Laadt een installer-step partial via AJAX
     *
     * @param {number|string} step
     */
    async loadStep(step) {
        const container = document.getElementById('installer-step');

        container.innerHTML = '<p>Loading…</p>';

        const res = await fetch(`/installer/step/${step}`);
        const data = await res.json();

        container.innerHTML = data.html;

        // Alleen sidebar/progress updaten bij stappen > 1
        if (data.meta?.step && data.meta.step !== 'environment') {
            Installer.updateSidebar(data.meta);
            Installer.updateProgressFromStep(data.meta.step);
        }

    //  AUTO-RUN steps
    switch (data.meta?.step) {
        case 'finalize':
            Installer.runFinalize();
            break;
    }


     },

    /**
     * Stap 1: Environment check
     * - Roept backend aan
     * - Toont fouten of succes
     * - Laat gebruiker expliciet doorgaan
     */
    async runEnvironmentCheck() {
        const button  = document.getElementById('env-check-btn');
        const results = document.getElementById('results');

        // UX: knop tijdelijk disablen
        button.disabled = true;
        button.innerText = 'Checking…';

        const res = await fetch('/installer/run', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ step: 'environment' })
        });

        const data = await res.json();

if (data.meta) {
    Installer.updateSidebar(data.meta);
}
if (data.progress) {
    Installer.updateProgress(data.progress);
}


        if (data.status === 'error') {
            results.innerHTML = data.errors
                .map(e => `<div class="error">❌ ${e}</div>`)
                .join('');

            // knop weer actief maken bij fout
            button.disabled = false;
            button.innerText = 'Run system check';
            return;
        }

        // succes → knop weg, resultaat tonen
        button.style.display = 'none';

        results.innerHTML = `
            <div>✅ Environment looks good</div>
            <div class="actions" style="margin-top:1rem">
               <button onclick="Installer.loadStep(2)">Continue</button>
            </div>
        `;
    },

    /**
     * Stap 2: Database configuratie
     * - Verzamelt form data
     * - Valideert via backend
     * - Bij succes → volgende stap
     */
    async submitDatabase() {
        const errors = document.getElementById('db-errors');

        const payload = {
            dbHost: document.getElementById('dbHost').value,
            dbName: document.getElementById('dbName').value,
            dbUser: document.getElementById('dbUser').value,
            dbPass: document.getElementById('dbPass').value,
        };

        const res = await fetch('/installer/run', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                step: 'database',
                payload
            })
        });

        const data = await res.json();

if (data.meta) {
    Installer.updateSidebar(data.meta);
}
if (data.progress) {
    Installer.updateProgress(data.progress);
}

        if (data.status === 'error') {
            errors.innerHTML = data.errors
                .map(e => `<div class="error">❌ ${e}</div>`)
                .join('');
            return;
        }

        // succes → door naar migrations
        this.loadStep(3);
    },

    /**
     * Stap 3: Database connectie + migrations
     * - Start automatisch
     * - Toont status
     * - Bij succes → continue
     */
    async runMigrations() {
        const status = document.getElementById('migration-status');

        status.innerHTML = '<p>Running migrations…</p>';

        const res = await fetch('/installer/run', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ step: 'migration' })
        });

        const data = await res.json();

if (data.meta) {
    Installer.updateSidebar(data.meta);
}
if (data.progress) {
    Installer.updateProgress(data.progress);
}

        if (data.status === 'error') {
            status.innerHTML = data.errors
                .map(e => `<div class="error">❌ ${e}</div>`)
                .join('');
            return;
        }

        status.innerHTML = `
            <div>✅ Database setup completed</div>
            <div class="actions" style="margin-top:1rem">
                <button onclick="Installer.loadStep(4)">Continue</button>
            </div>
        `;
    },

    /**
     * Stap 4: Admin user aanmaken
     * - Start automatisch
     * - Toont status
     * - Bij succes → continue
     */
    async submitAdmin() {
        const errors = document.getElementById('admin-errors');

        const payload = {
            adminName: document.getElementById('adminName').value,
            adminEmail: document.getElementById('adminEmail').value
        };

        const res = await fetch('/installer/run', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                step: 'admin',
                payload
            })
    });

    const data = await res.json();


if (data.meta) {
    Installer.updateSidebar(data.meta);
}
if (data.progress) {
    Installer.updateProgress(data.progress);
}

    if (data.status === 'error') {
        errors.innerHTML = data.errors
            .map(e => `<div class="error">❌ ${e}</div>`)
            .join('');
        return;
    }

    // 🔥 FINALIZE TECHNISCH UITVOEREN
    await Installer.runFinalize();

    Installer.loadStep(5);
    },

async runFinalize() {
    const res = await fetch('/installer/run', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ step: 'finalize' })
    });

    const data = await res.json();

    if (data.status === 'error') {
        alert(
            'Installation failed during finalization:\n\n' +
            data.errors.join('\n')
        );
        throw new Error('Finalize failed');
    }

    if (data.meta) {
        Installer.updateSidebar(data.meta);
    }
    if (data.progress) {
        Installer.updateProgress(data.progress);
        }
    },

 async completeInstallation(button) {

   if (!button) {
        console.error('completeInstallation called without button');
        return;
    }

    button.disabled = true;
    button.innerText = 'Installation finalizing…';

    const res = await fetch('/installer/commit', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
    });

    const data = await res.json();

    if (data.status !== 'ok') {
        alert(data.errors?.join('\n') ?? 'Installation failed');
        button.disabled = false;
        button.innerText = 'Installatie afronden';
        return;
    }

    window.location.href = '/login';
    }
};




Installer.stepOrder = [
    'environment',
    'database',
    'migration',
    'admin',
    'finalize'
];

Installer.updateProgressFromStep = function (stepName) {
    const index = Installer.stepOrder.indexOf(stepName);
    if (index === -1) return;

    const current = index + 1;
    const total   = Installer.stepOrder.length;
    const percent = Math.round((current / total) * 100);

    Installer.updateProgress({
        current,
        total,
        percent
    });
};


Installer.updateSidebar = function (meta) {


    const title = document.getElementById('wizard-step-title');
    const desc  = document.getElementById('wizard-step-description');

    if (!title || !desc || !meta) {
        return;
    }

    title.textContent = meta.title ?? '';
    desc.textContent  = meta.description ?? '';
};


Installer.updateProgress = function (progress) {
    if (!progress) return;

    const fill  = document.getElementById('installer-progress-fill');
    const label = document.getElementById('installer-progress-label');

    if (!fill || !label) return;

    fill.style.width = progress.percent + '%';
    label.textContent =
        `Step ${progress.current} of ${progress.total}`;
};



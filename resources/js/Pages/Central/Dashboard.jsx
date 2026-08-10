import { router } from '@inertiajs/react';

function StatCard({ label, value, hint }) {
    return (
        <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <p className="text-sm text-gray-500">{label}</p>
            <p className="mt-1 text-3xl font-bold">{value}</p>
            {hint && <p className="mt-1 text-xs text-gray-400">{hint}</p>}
        </div>
    );
}

export default function Dashboard({ mrr, arr, activeSubscribers, churnRate }) {
    return (
        <div className="min-h-screen bg-gray-50 p-8 font-sans text-gray-900">
            <nav className="mb-8 flex gap-4 text-sm">
                <a href="/admin/dashboard" className="font-semibold text-blue-600">Dashboard</a>
                <a href="/admin/tenants" className="text-gray-600 hover:text-gray-900">Tenants</a>
                <a href="/admin/plans" className="text-gray-600 hover:text-gray-900">Plans</a>
                <a href="/horizon" className="text-gray-600 hover:text-gray-900">Horizon</a>
                <button
                    type="button"
                    onClick={() => router.post('/logout')}
                    className="ml-auto text-gray-500 hover:text-gray-900"
                >
                    Log out
                </button>
            </nav>

            <h1 className="mb-8 text-2xl font-bold">Revenue dashboard</h1>

            <div className="grid grid-cols-2 gap-4 md:grid-cols-4">
                <StatCard label="MRR" value={`$${mrr.toFixed(2)}`} hint="Monthly recurring revenue" />
                <StatCard label="ARR" value={`$${arr.toFixed(2)}`} hint="MRR × 12" />
                <StatCard label="Active subscribers" value={activeSubscribers} />
                <StatCard label="Churn rate (30d)" value={`${churnRate}%`} hint="Logo churn, trailing 30 days" />
            </div>
        </div>
    );
}

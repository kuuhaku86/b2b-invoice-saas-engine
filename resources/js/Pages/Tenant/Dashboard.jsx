import { router } from '@inertiajs/react';

const STATUS_LABELS = {
    draft: 'Draft',
    sent: 'Sent',
    paid: 'Paid',
    past_due: 'Past due',
    cancelled: 'Cancelled',
};

const STATUS_COLORS = {
    draft: 'bg-gray-400',
    sent: 'bg-blue-500',
    paid: 'bg-green-500',
    past_due: 'bg-amber-500',
    cancelled: 'bg-red-500',
};

export default function Dashboard({ tenantId, user, invoiceStatusBreakdown }) {
    const rows = Object.entries(invoiceStatusBreakdown);
    const totalCount = rows.reduce((sum, [, row]) => sum + row.count, 0);

    return (
        <div className="min-h-screen bg-gray-50 p-8 font-sans text-gray-900">
            <nav className="mb-8 flex gap-4 text-sm">
                <a href="/dashboard" className="font-semibold text-blue-600">Dashboard</a>
                <a href="/clients" className="text-gray-600 hover:text-gray-900">Clients</a>
                <a href="/invoices" className="text-gray-600 hover:text-gray-900">Invoices</a>
                <a href="/recurring" className="text-gray-600 hover:text-gray-900">Recurring</a>
                <a href="/billing" className="text-gray-600 hover:text-gray-900">Billing</a>
            </nav>

            <h1 className="mb-1 text-2xl font-bold">Dashboard</h1>
            <p className="mb-8 text-sm text-gray-500">
                Tenant: {tenantId} — {user.name} ({user.email})
            </p>

            <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h2 className="mb-4 text-lg font-semibold">Invoice status breakdown</h2>

                {totalCount === 0 ? (
                    <p className="text-sm text-gray-500">No invoices yet.</p>
                ) : (
                    <>
                        <div className="mb-4 flex h-4 overflow-hidden rounded-full bg-gray-100">
                            {rows.map(([status, row]) => (
                                <div
                                    key={status}
                                    className={STATUS_COLORS[status] ?? 'bg-gray-400'}
                                    style={{ width: `${(row.count / totalCount) * 100}%` }}
                                    title={`${STATUS_LABELS[status] ?? status}: ${row.count}`}
                                />
                            ))}
                        </div>

                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b border-gray-200 text-left text-gray-500">
                                    <th className="py-2">Status</th>
                                    <th className="py-2">Count</th>
                                    <th className="py-2">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                {rows.map(([status, row]) => (
                                    <tr key={status} className="border-b border-gray-100">
                                        <td className="flex items-center gap-2 py-2">
                                            <span className={`h-2 w-2 rounded-full ${STATUS_COLORS[status] ?? 'bg-gray-400'}`} />
                                            {STATUS_LABELS[status] ?? status}
                                        </td>
                                        <td className="py-2">{row.count}</td>
                                        <td className="py-2">${row.total.toFixed(2)}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </>
                )}
            </div>

            <button
                type="button"
                onClick={() => router.post('/logout')}
                className="mt-8 text-sm text-gray-500 hover:text-gray-900"
            >
                Log out
            </button>
        </div>
    );
}

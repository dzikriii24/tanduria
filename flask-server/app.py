from flask import Flask, jsonify, request
import pandas as pd

app = Flask(__name__)

# Load CSV, exclude Unnamed columns
data = pd.read_csv('datahargapadijawa.csv', sep=';')
data = data.loc[:, ~data.columns.str.contains('^Unnamed')]
data['Tanggal'] = pd.to_datetime(data['Tanggal'], dayfirst=True)

bulan_mapping = {
    "January": "Januari", "February": "Februari", "March": "Maret",
    "April": "April", "May": "Mei", "June": "Juni",
    "July": "Juli", "August": "Agustus", "September": "September",
    "October": "Oktober", "November": "November", "December": "Desember"
}
data['month'] = data['Tanggal'].dt.month_name().map(bulan_mapping)
data['year'] = data['Tanggal'].dt.year

# Data transform, long format
data_long = data.melt(
    id_vars=['Tanggal', 'month', 'year'],
    var_name='province',
    value_name='price'
)

# Filter untuk memastikan hanya provinsi valid dan price valid
valid_provinces = ['Banten', 'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur']
data_long = data_long[
    data_long['province'].isin(valid_provinces) & 
    pd.notna(data_long['price']) & 
    (data_long['price'] > 0)
]

@app.route('/api/gkp-per-bulan')
def gkp_per_bulan():
    province = request.args.get('province')
    year = request.args.get('year', type=int)

    filtered = data_long.copy()

    if province:
        filtered = filtered[filtered['province'] == province]
    if year:
        filtered = filtered[filtered['year'] == year]

    filtered = filtered[filtered['price'].notna()]
    filtered = filtered[filtered['price'] > 0]

    result = (
        filtered
        .groupby(['month'])
        .agg(avg_price=('price', 'mean'))
        .reset_index()
        .sort_values(by='month', key=lambda col: pd.Categorical(col, categories=list(bulan_mapping.values()), ordered=True))
        .to_dict(orient='records')
    )

    return jsonify({
        'province': province,
        'year': year,
        'data': result
    })

@app.route('/api/gkp-per-bulan-semua-provinsi')
def gkp_per_bulan_semua_provinsi():
    year = request.args.get('year', type=int)

    if not year:
        return jsonify({'error': 'year parameter is required'}), 400

    filtered = data_long[data_long['year'] == year]
    filtered = filtered[filtered['price'].notna()]
    filtered = filtered[filtered['price'] > 0]

    result = (
        filtered.groupby(['province', 'month'])
        .agg(avg_price=('price', 'mean'))
        .reset_index()
        .sort_values(by=['province', 'month'], key=lambda col: pd.Categorical(
            col, categories=list(bulan_mapping.values()), ordered=True) if col.name == 'month' else col)
        .to_dict(orient='records')
    )

    return jsonify({'year': year, 'data': result})

@app.route('/api/gkp-summary-latest')
def gkp_summary_latest():
    latest_date = data_long['Tanggal'].max()

    filtered = data_long[
        (data_long['Tanggal'].dt.month == latest_date.month) &
        (data_long['Tanggal'].dt.year == latest_date.year)
    ]

    summary = (
        filtered
        .groupby('province')
        .agg(avg_price=('price', 'mean'))
        .reset_index()
        .to_dict(orient='records')
    )

    return jsonify({
        'month': bulan_mapping[latest_date.month_name()],
        'year': latest_date.year,
        'data': summary
    })

@app.route('/api/gkp-avg-summary-latest')
def gkp_avg_summary_latest():
    latest_date = data_long['Tanggal'].max()

    filtered = data_long[
        (data_long['Tanggal'].dt.month == latest_date.month) &
        (data_long['Tanggal'].dt.year == latest_date.year)
    ]

    avg_price = filtered['price'].mean() if not filtered.empty else 0

    return jsonify({
        'month': bulan_mapping[latest_date.month_name()],
        'year': latest_date.year,
        'avg_price': round(avg_price)
    })

@app.route('/api/gkp-full-data')
def gkp_full_data():
    result = data_long.to_dict(orient='records')
    return jsonify(result)

if __name__ == '__main__':
    print("Menjalankan Flask server di http://127.0.0.1:5000")
    app.run(debug=True, port=5000)

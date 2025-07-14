from flask import Flask, jsonify, request
import pandas as pd

app = Flask(__name__)

# Load data sekali saat app mulai
data = pd.read_csv('datahargapadijawa.csv', sep=';')
data['Tanggal'] = pd.to_datetime(data['Tanggal'], dayfirst=True)
bulan_mapping = {
    "January": "Januari", "February": "Februari", "March": "Maret",
    "April": "April", "May": "Mei", "June": "Juni",
    "July": "Juli", "August": "Agustus", "September": "September",
    "October": "Oktober", "November": "November", "December": "Desember"
}
data['month'] = data['Tanggal'].dt.month_name().map(bulan_mapping)
data['year'] = data['Tanggal'].dt.year
data_long = data.melt(id_vars=['Tanggal', 'month', 'year'], var_name='province', value_name='price')

# Route filterable API
@app.route('/api/gkp-full-data', methods=['GET'])
def gkp_full_data():
    province = request.args.get('province')
    month = request.args.get('month')

    filtered = data_long.copy()

    if province:
        filtered = filtered[filtered['province'] == province]
    if month:
        filtered = filtered[filtered['month'] == month]

    result = filtered.to_dict(orient='records')
    return jsonify(result)

@app.route("/api/gkp-harian")
def gkp_harian():
    # filter pakai request.args.get('month') dan request.args.get('province')
    return jsonify(list_of_harga_per_hari)

if __name__ == '__main__':
    app.run(debug=True)

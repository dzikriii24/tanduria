from flask import Flask, jsonify
from scraper import get_bps_data

app = Flask(__name__)

@app.route('/api/bps-data', methods=['GET'])
def bps_data():
    df = get_bps_data()
    # Filter GKP: '1.b. Gabah Kering Panen (GKP) di Tingkat Petani'
    gkp = df[df['Quality'] == '1.b. Gabah Kering Panen (GKP) di Tingkat Petani']
    data = {
        'gkp': gkp[['Bulan', '2024']].rename(columns={'2024':'harga'}).to_dict(orient='records'),
        'beras': df[df['Quality'].str.contains('Beras')][['Quality','2024']].rename(columns={'Quality':'kategori','2024':'harga'}).to_dict(orient='records')
    }
    return jsonify(data)

if __name__ == '__main__':
    app.run(debug=True)

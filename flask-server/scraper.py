import requests
import pandas as pd

def get_bps_data():
    url = "https://jabar.bps.go.id/api/tabel/2/MjI2IzI=/harga-gabah-dan-beras?format=json"
    resp = requests.get(url)
    resp.raise_for_status()
    js = resp.json()

    cols = [c['title'] for c in js['columns']]
    df = pd.DataFrame(js['data'], columns=cols)

    return df

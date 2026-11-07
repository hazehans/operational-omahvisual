import openpyxl
import os
import json
import re

base_ga_dir = r"references\General_Affair_INVENTARIS-20261107T012453Z-1-001\INVENTARIS"
output_json_path = r"database\data\ga_inventory_dataset.json"

os.makedirs(os.path.dirname(output_json_path), exist_ok=True)

data = {
    "vehicles": [],
    "rental_products": [],
    "processors": [],
    "laptops": [],
    "toolboxes": [],
    "toolkit_operators": [],
    "internal_office_assets": []
}

# 1. KENDARAAN
veh_path = os.path.join(base_ga_dir, "DATA ASET KENDARAAN_.xlsx")
if os.path.exists(veh_path):
    wb = openpyxl.load_workbook(veh_path, data_only=True)
    if "List Kendaraan" in wb.sheetnames:
        ws = wb["List Kendaraan"]
        rows = list(ws.iter_rows(values_only=True))
        for r in rows[3:]:
            if r[0] and str(r[0]).startswith("KND"):
                data["vehicles"].append({
                    "vehicle_code": str(r[0]).strip(),
                    "name": str(r[1]).strip() if r[1] else "",
                    "plate_number": str(r[2]).strip() if r[2] else "",
                    "frame_number": str(r[3]).strip() if r[3] else "",
                    "type": str(r[4]).strip() if r[4] else "Pick up",
                    "year": int(r[5]) if r[5] else None,
                    "status": "tersedia"
                })

# 2. LAPTOP
lap_path = os.path.join(base_ga_dir, "DATA LAPTOP.xlsx")
if os.path.exists(lap_path):
    wb = openpyxl.load_workbook(lap_path, data_only=True)
    ws = wb.active
    rows = list(ws.iter_rows(values_only=True))
    for r in rows[4:]:
        # Column 1 is No. (1.0, 2.0...)
        if len(r) > 4 and r[1] and isinstance(r[1], (int, float)):
            pic = str(r[2]).strip() if r[2] else ""
            no_lap = str(int(r[3])) if r[3] and isinstance(r[3], (int, float)) else str(r[3] or "")
            lap_type = str(r[4]).strip() if r[4] else "Laptop"
            status_raw = str(r[5]).strip().lower() if len(r) > 5 and r[5] else "ready"
            notes = str(r[6]).strip() if len(r) > 6 and r[6] else ""
            data["laptops"].append({
                "name": f"Laptop {no_lap} - {lap_type} (PIC: {pic})",
                "serial_number": f"LAP-{no_lap.zfill(2)}",
                "pic": pic,
                "model": lap_type,
                "condition": "available" if "ready" in status_raw and "no" not in status_raw else "damaged",
                "status": "in_storage",
                "notes": notes
            })

# 3. TOOLBOX
tb_path = os.path.join(base_ga_dir, "DATA TOOLS BOX.xlsx")
if os.path.exists(tb_path):
    wb = openpyxl.load_workbook(tb_path, data_only=True)
    if "DASHBOARD TOOLBOX" in wb.sheetnames:
        ws = wb["DASHBOARD TOOLBOX"]
        for r in list(ws.iter_rows(values_only=True))[2:10]:
            if r[0] and str(r[0]).startswith("TB"):
                data["toolboxes"].append({
                    "code": str(r[0]).strip(),
                    "name": f"Tool Box {str(r[0]).strip()} (Isi 14 Item)",
                    "completeness": str(r[1]).strip() if r[1] else "14/14",
                    "condition": "available" if str(r[2]).lower() == "aman" else "damaged",
                    "status": "in_storage"
                })

# 4. INVENTORY OMVIS LED 2026
omvis_path = os.path.join(base_ga_dir, "INVENTORY OMVIS LED 2026.xlsx")
if os.path.exists(omvis_path):
    wb = openpyxl.load_workbook(omvis_path, data_only=True)
    
    # Sheet: Data Stok Rental LED
    if "Data Stok Rental LED" in wb.sheetnames:
        ws = wb["Data Stok Rental LED"]
        current_subgroup = "P3.9 - SAGA LITE"
        for r in ws.iter_rows(values_only=True):
            if not any(r):
                continue
            # Look for headers / subgroup names in the row
            row_str = " ".join([str(c) for c in r if c is not None])
            if "P3.9" in row_str or "P2.6" in row_str or "MAVIC" in row_str or "DATA BARANG LAINNYA" in row_str:
                current_subgroup = row_str
                continue
            if "Update" in row_str or "Nama Barang" in row_str:
                continue

            # In this sheet, col 0 is No., col 2 is Nama Barang, col 3 is Jumlah, col 4 is Satuan, col 5 is Keterangan
            if isinstance(r[0], (int, float)) and len(r) > 2 and r[2]:
                name = str(r[2]).strip()
                qty_raw = r[3] if len(r) > 3 else 0
                satuan = str(r[4]).strip() if len(r) > 4 and r[4] else "pcs"
                ket = str(r[5]).strip() if len(r) > 5 and r[5] else ""

                try:
                    qty = float(qty_raw)
                except:
                    nums = re.findall(r"[-+]?\d*\.\d+|\d+", str(qty_raw))
                    qty = float(nums[0]) if nums else 0.0

                if name:
                    sqm = None
                    if "100 x 50" in name:
                        sqm = 0.50
                    elif "50 x 50" in name:
                        sqm = 0.25

                    cat = "Komponen Videotron"
                    if "Hardcase" in name:
                        cat = "Penyimpanan & Hardcase"
                    elif "Power" in name or "Kabel" in name or "LAN" in name or "FOH" in name:
                        cat = "Perkabelan"
                    elif "Panel" in name or "Konektor Panel" in name:
                        cat = "Kelistrikan & Power"
                    elif "Besi" in name or "Rigging" in name or "Panggung" in name or "Takel" in name or "Clamp" in name or "Webbing" in name:
                        cat = "Konstruksi & Rigging"
                    elif "Baut" in name or "Kupu" in name:
                        cat = "Peralatan & Tools"

                    data["rental_products"].append({
                        "name": name,
                        "subgroup": current_subgroup,
                        "qty": qty,
                        "unit": satuan,
                        "sqm_per_unit": sqm,
                        "category": cat,
                        "keterangan": ket,
                        "is_rental_item": True
                    })

    # Sheet: ASET Ruang Office (Internal)
    if "ASET Ruang Office" in wb.sheetnames:
        ws = wb["ASET Ruang Office"]
        for r in ws.iter_rows(values_only=True):
            if isinstance(r[0], (int, float)) and len(r) > 1 and r[1]:
                name = str(r[1]).strip()
                qty = float(r[2]) if len(r) > 2 and isinstance(r[2], (int, float)) else 1.0
                satuan = str(r[3]).strip() if len(r) > 3 and r[3] else "Unit"
                ket = str(r[4]).strip() if len(r) > 4 and r[4] else ""
                data["internal_office_assets"].append({
                    "name": name,
                    "qty": qty,
                    "unit": satuan,
                    "category": "Aset Kantor & Mess",
                    "keterangan": ket,
                    "is_rental_item": False
                })

    # Sheet: Mess Staf Dormitory
    if "Mess  Staf Dormitory" in wb.sheetnames:
        ws = wb["Mess  Staf Dormitory"]
        for r in ws.iter_rows(values_only=True):
            if isinstance(r[0], (int, float)) and len(r) > 1 and r[1]:
                name = str(r[1]).strip()
                qty = float(r[2]) if len(r) > 2 and isinstance(r[2], (int, float)) else 1.0
                satuan = str(r[3]).strip() if len(r) > 3 and r[3] else "Item"
                ket = str(r[4]).strip() if len(r) > 4 and r[4] else ""
                data["internal_office_assets"].append({
                    "name": f"Mess: {name}",
                    "qty": qty,
                    "unit": satuan,
                    "category": "Aset Kantor & Mess",
                    "keterangan": ket,
                    "is_rental_item": False
                })

# 5. PROCESSORS (From SPK Mba De)
procs = [
    {"name": "Novastar Video Processor VX2", "code": "PROC-VX2", "brand": "NovaStar", "model": "VX2"},
    {"name": "Novastar Video Processor VX4", "code": "PROC-VX4", "brand": "NovaStar", "model": "VX4"},
    {"name": "Novastar Video Processor VX4S", "code": "PROC-VX4S", "brand": "NovaStar", "model": "VX4S"},
    {"name": "Novastar Video Processor VX6S", "code": "PROC-VX6S", "brand": "NovaStar", "model": "VX6S"},
    {"name": "Novastar Video Processor VX8", "code": "PROC-VX8", "brand": "NovaStar", "model": "VX8"},
    {"name": "Novastar Video Processor VX10", "code": "PROC-VX10", "brand": "NovaStar", "model": "VX10"},
    {"name": "Novastar Video Processor X20M", "code": "PROC-X20M", "brand": "NovaStar", "model": "X20M"},
]
data["processors"] = procs

with open(output_json_path, "w", encoding="utf-8") as f:
    json.dump(data, f, indent=2, ensure_ascii=False)

print(f"Extraction complete! Vehicles: {len(data['vehicles'])}, Rental Products: {len(data['rental_products'])}, Internal Assets: {len(data['internal_office_assets'])}, Processors: {len(data['processors'])}, Laptops: {len(data['laptops'])}, Toolboxes: {len(data['toolboxes'])}")

import os
import re

directories = ['resources/views']

for root, _, files in os.walk(directories[0]):
    for file in files:
        if file.endswith('.blade.php'):
            path = os.path.join(root, file)
            with open(path, 'r', encoding='utf-8') as f:
                content = f.read()

            original = content
            
            # Replace btn-primary with btn--primary
            content = content.replace('class="btn btn-primary"', 'class="btn btn--primary"')
            content = content.replace('class="btn btn-secondary"', 'class="btn btn--secondary"')
            content = content.replace('class="btn btn-success"', 'class="btn btn--success"')
            content = content.replace('class="btn btn-danger"', 'class="btn btn--danger"')
            content = content.replace('class="btn btn-warning"', 'class="btn btn--warning"')
            content = content.replace('class="btn btn-info"', 'class="btn btn--info"')
            
            # Replace + with SVG for common buttons
            content = content.replace('>+ Buat Permintaan ATK<', '><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Buat Permintaan ATK<')
            content = content.replace('>+ Tambah ATK<', '><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Tambah ATK<')
            content = content.replace('>+ Tambah Aset<', '><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Tambah Aset<')
            content = content.replace('>+ Tambah Kendaraan<', '><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Tambah Kendaraan<')
            content = content.replace('>+ Tambah Transaksi<', '><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Tambah Transaksi<')
            content = content.replace('>+ Catat Pemakaian<', '><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Catat Pemakaian<')
            content = content.replace('>+ Catat Pemeliharaan<', '><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Catat Pemeliharaan<')
            content = content.replace('>+ Catat Perjalanan<', '><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Catat Perjalanan<')
            content = content.replace('>+ Catat BBM<', '><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Catat BBM<')
            content = content.replace('>+ Catat Laporan<', '><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Catat Laporan<')
            content = content.replace('>+ Catat Evaluasi<', '><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Catat Evaluasi<')

            # Replace form-control with input
            content = content.replace('class="form-control"', 'class="input"')
            content = content.replace('class="form-select"', 'class="select"')
            
            # Simple attribute addition for inputs that don't have class
            # Only add to inputs with specific types to avoid checkboxes/radios/hidden
            def add_input_class(match):
                tag = match.group(0)
                if 'class=' in tag:
                    return tag
                type_match = re.search(r'type="(text|number|email|date|password|url)"', tag)
                if type_match or 'type=' not in tag:
                    return tag.replace('<input ', '<input class="input" ')
                return tag
                
            content = re.sub(r'<input\s+[^>]*>', add_input_class, content)
            
            def add_select_class(match):
                tag = match.group(0)
                if 'class=' in tag:
                    return tag
                return tag.replace('<select ', '<select class="select" ')
                
            content = re.sub(r'<select\s+[^>]*>', add_select_class, content)
            
            def add_textarea_class(match):
                tag = match.group(0)
                if 'class=' in tag:
                    return tag
                return tag.replace('<textarea ', '<textarea class="textarea" ')
                
            content = re.sub(r'<textarea\s+[^>]*>', add_textarea_class, content)

            if content != original:
                with open(path, 'w', encoding='utf-8') as f:
                    f.write(content)
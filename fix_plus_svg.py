import os
import re

for root, _, files in os.walk('resources/views'):
    for file in files:
        if file.endswith('.blade.php'):
            path = os.path.join(root, file)
            with open(path, 'r', encoding='utf-8') as f:
                content = f.read()

            original = content
            
            # Replace inline "+ Text" inside buttons/anchors with SVG
            # We look for something like: >\s*\+\s*Text\s*<
            content = re.sub(r'(>)\s*\+\s*([a-zA-Z0-9_\s]+?)\s*(<)', r'\1<svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> \2\3', content)

            if content != original:
                with open(path, 'w', encoding='utf-8') as f:
                    f.write(content)
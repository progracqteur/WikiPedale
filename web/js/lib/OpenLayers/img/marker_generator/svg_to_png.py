import os, sys, subprocess

sys.stdout.write('path to inkscape [/Applications/Inkscape.app/Contents/Resources/bin/inkscape]')
path_to_inkscape = '/Applications/Inkscape.app/Contents/Resources/bin/inkscape'
new_path_to_inkscape = raw_input()
if new_path_to_inkscape != '' :
    path_to_inkscape = new_path_to_inkscape

for fn in os.listdir('..') :
    if fn[-3:] ==  'svg' :
        svg_fn = '../' + fn
        png_fn = '../' + fn[:-3] + 'png'
        subprocess.call([path_to_inkscape, '-z', '-f', svg_fn, '-j', '-e', png_fn])

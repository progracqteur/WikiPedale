base_dest = [['m_short_base.svg', '../m_short_'],
             ['m_med_base.svg', '../m_med_'],
             ['m_hard_base.svg', '../m_hard_'],
             ['m_base.svg', '../m_']]

colors = [['g','66cd00'],
          ['w','ffffff'],
          ['r','ff0000'],
          ['o','cda000'],
          ['d','696969']]

mode = [['','000000'],
        ['_selected','ff34b3'],
        ['_no_active','838383']]

for bd in base_dest :
    base = bd[0]
    dest = bd[1]
    f_img_base = open(base, 'r')
    img_base = f_img_base.read()

    for cc in colors :
        for mm in mode : 
            img = img_base.replace('ff0000',cc[1]) #remplacement du rouge par autre couleur
            img = img.replace('000000',mm[1]) #remplacement du noir par autre couleur
            f = open(dest + cc[0] + mm[0] + '.svg','w')
            f.write(img)
            f.close()
    f_img_base.close()
            
    

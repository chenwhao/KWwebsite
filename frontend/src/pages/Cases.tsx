import React, { useState } from 'react'
import { motion, AnimatePresence } from 'framer-motion'
import { 
  Search,
  Filter,
  Calendar,
  MapPin,
  Users,
  Eye,
  ChevronLeft,
  ChevronRight,
  X
} from 'lucide-react'

const Cases: React.FC = () => {
  const [selectedCategory, setSelectedCategory] = useState('全部')
  const [searchTerm, setSearchTerm] = useState('')
  const [selectedCase, setSelectedCase] = useState<any>(null)

  // 案例分类
  const categories = ['全部', '科技展', '汽车展', '医疗展', '教育展', '金融展', '其他']

  // 案例数据
  const cases = [
    {
      id: 1,
      title: '华为科技创新展台',
      category: '科技展',
      client: '华为技术有限公司',
      date: '2024年3月',
      location: '上海国家会展中心',
      area: '120㎡',
      visitors: '2000+',
      image: '/images/tech-display-1.webp',
      description: '为华为公司设计的科技创新展台，融合现代科技元素与企业文化，采用简约现代的设计风格，突出产品展示和互动体验。',
      gallery: [
        '/images/tech-display-1.webp',
        '/images/exhibition-booth-1.jpg',
        '/images/creative-display-1.jpg'
      ],
      features: [
        '现代简约设计风格',
        '智能互动展示系统',
        'LED大屏幕展示',
        'VR体验区设置',
        '产品展示柜定制',
        '洽谈区域配置'
      ],
      challenges: '在有限的空间内展示多款科技产品，同时确保良好的观众流线和互动体验。',
      solution: '采用分层展示方案，通过垂直空间利用最大化展示效果，设置多个互动体验区。'
    },
    {
      id: 2,
      title: '奔驰豪华汽车展台',
      category: '汽车展',
      client: '梅赛德斯-奔驰',
      date: '2024年4月',
      location: '上海汽车展览中心',
      area: '200㎡',
      visitors: '5000+',
      image: '/images/luxury-booth-1.jpg',
      description: '高端汽车品牌展台设计，彰显品牌奢华与精致工艺，采用优质材料和精细工艺打造完美展示空间。',
      gallery: [
        '/images/luxury-booth-1.jpg',
        '/images/exhibition-hall-1.jpg',
        '/images/booth-construction-1.jpg'
      ],
      features: [
        '奢华品牌形象设计',
        '车型展示平台',
        '灯光氛围营造',
        '高端材料运用',
        'VIP接待区域',
        '品牌文化展示'
      ],
      challenges: '如何在展台设计中完美体现奢华品牌形象，同时确保车型展示效果。',
      solution: '运用高端材料和精致工艺，配合专业灯光设计，营造奢华氛围。'
    },
    {
      id: 3,
      title: '创意设计工作室展台',
      category: '其他',
      client: '某知名设计公司',
      date: '2024年2月',
      location: '北京国际设计周',
      area: '80㎡',
      visitors: '1500+',
      image: '/images/creative-display-1.jpg',
      description: '充满创意的设计展台，展现设计师的创新理念和艺术追求，采用独特的空间布局和视觉表现。',
      gallery: [
        '/images/creative-display-1.jpg',
        '/images/design-process-1.jpg',
        '/images/office-workspace-1.png'
      ],
      features: [
        '创意空间设计',
        '艺术装置展示',
        '作品展示系统',
        '互动体验区',
        '设计师工作区',
        '创意交流空间'
      ],
      challenges: '在展示设计作品的同时，体现设计师的创意思维和工作过程。',
      solution: '打造开放式工作展示区，让观众能够近距离观察设计过程。'
    },
    {
      id: 4,
      title: '医疗器械专业展台',
      category: '医疗展',
      client: '西门子医疗',
      date: '2024年1月',
      location: '广州医疗器械展览馆',
      area: '150㎡',
      visitors: '3000+',
      image: '/images/exhibition-visitors-1.webp',
      description: '专业医疗器械展台设计，突出产品的专业性和科技感，为医疗行业专业人士提供良好的交流平台。',
      gallery: [
        '/images/exhibition-visitors-1.webp',
        '/images/tech-display-1.webp',
        '/images/exhibition-booth-1.jpg'
      ],
      features: [
        '专业医疗器械展示',
        '产品演示区域',
        '技术交流空间',
        '专业资料展示',
        '洁净展示环境',
        '专业人员接待'
      ],
      challenges: '医疗器械展示需要专业的环境和严格的标准。',
      solution: '采用洁净环境设计，确保产品展示的专业性和安全性。'
    },
    {
      id: 5,
      title: '教育科技展台',
      category: '教育展',
      client: '新东方教育',
      date: '2023年12月',
      location: '深圳教育装备展览馆',
      area: '100㎡',
      visitors: '2500+',
      image: '/images/booth-construction-1.jpg',
      description: '教育科技展台设计，展示现代教育技术和教学理念，为教育工作者提供体验和交流的平台。',
      gallery: [
        '/images/booth-construction-1.jpg',
        '/images/team-meeting-1.jpg',
        '/images/creative-display-1.jpg'
      ],
      features: [
        '教育产品展示',
        '互动教学演示',
        '多媒体展示系统',
        '学习体验区',
        '教师交流空间',
        '技术支持区域'
      ],
      challenges: '如何在展台中体现现代教育理念和技术创新。',
      solution: '设置多个互动体验区，让观众能够亲身体验教育科技产品。'
    },
    {
      id: 6,
      title: '金融服务展台',
      category: '金融展',
      client: '中国银行',
      date: '2023年11月',
      location: '上海金融博览会',
      area: '180㎡',
      visitors: '4000+',
      image: '/images/office-workspace-1.png',
      description: '金融服务展台设计，体现金融机构的专业性和可信度，为客户提供便捷的服务体验。',
      gallery: [
        '/images/office-workspace-1.png',
        '/images/team-meeting-1.jpg',
        '/images/exhibition-hall-1.jpg'
      ],
      features: [
        '专业金融服务展示',
        '客户咨询区域',
        '数字化服务体验',
        '安全防护措施',
        'VIP客户接待',
        '品牌形象展示'
      ],
      challenges: '金融展台需要体现专业性和安全性，同时保持亲和力。',
      solution: '采用稳重的设计风格，配合现代化的服务展示系统。'
    }
  ]

  // 过滤案例
  const filteredCases = cases.filter(caseItem => {
    const matchCategory = selectedCategory === '全部' || caseItem.category === selectedCategory
    const matchSearch = caseItem.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                       caseItem.client.toLowerCase().includes(searchTerm.toLowerCase())
    return matchCategory && matchSearch
  })

  // 打开案例详情
  const openCaseDetail = (caseItem: any) => {
    setSelectedCase(caseItem)
  }

  // 关闭案例详情
  const closeCaseDetail = () => {
    setSelectedCase(null)
  }

  return (
    <div className="bg-white">
      {/* 页面头部 */}
      <section className="relative bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
        <div 
          className="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20"
          style={{ backgroundImage: 'url(/images/exhibition-hall-1.jpg)' }}
        ></div>
        <div className="relative container mx-auto px-4 text-center">
          <motion.h1 
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            className="text-4xl lg:text-5xl font-bold mb-6"
          >
            案例展示
          </motion.h1>
          <motion.p 
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.2 }}
            className="text-xl text-blue-100 max-w-3xl mx-auto"
          >
            展示我们的专业能力和创意成果，每一个项目都体现我们的专业水准
          </motion.p>
        </div>
      </section>

      {/* 筛选和搜索 */}
      <section className="py-12 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="flex flex-col lg:flex-row gap-6 items-center justify-between">
            {/* 分类筛选 */}
            <div className="flex flex-wrap gap-2">
              {categories.map((category) => (
                <button
                  key={category}
                  onClick={() => setSelectedCategory(category)}
                  className={`px-4 py-2 rounded-lg font-medium transition-colors duration-200 ${
                    selectedCategory === category
                      ? 'bg-blue-600 text-white'
                      : 'bg-white text-gray-600 hover:bg-blue-50'
                  }`}
                >
                  {category}
                </button>
              ))}
            </div>

            {/* 搜索框 */}
            <div className="relative">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
              <input
                type="text"
                placeholder="搜索案例名称或客户..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="pl-10 pr-4 py-2 w-64 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>
        </div>
      </section>

      {/* 案例网格 */}
      <section className="py-20">
        <div className="container mx-auto px-4">
          <motion.div 
            layout
            className="grid md:grid-cols-2 lg:grid-cols-3 gap-8"
          >
            <AnimatePresence>
              {filteredCases.map((caseItem) => (
                <motion.div
                  key={caseItem.id}
                  layout
                  initial={{ opacity: 0, scale: 0.9 }}
                  animate={{ opacity: 1, scale: 1 }}
                  exit={{ opacity: 0, scale: 0.9 }}
                  transition={{ duration: 0.3 }}
                  className="group bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300 cursor-pointer"
                  onClick={() => openCaseDetail(caseItem)}
                >
                  <div className="relative overflow-hidden">
                    <img 
                      src={caseItem.image} 
                      alt={caseItem.title}
                      className="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                    <div className="absolute top-4 left-4 bg-blue-600 text-white px-3 py-1 rounded-full text-sm">
                      {caseItem.category}
                    </div>
                    <div className="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center">
                      <Eye className="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
                    </div>
                  </div>
                  
                  <div className="p-6">
                    <h3 className="text-xl font-bold text-gray-900 mb-2">{caseItem.title}</h3>
                    <p className="text-blue-600 font-semibold mb-2">{caseItem.client}</p>
                    <p className="text-gray-600 mb-4 line-clamp-2">{caseItem.description}</p>
                    
                    <div className="flex items-center justify-between text-sm text-gray-500">
                      <div className="flex items-center space-x-2">
                        <Calendar className="w-4 h-4" />
                        <span>{caseItem.date}</span>
                      </div>
                      <div className="flex items-center space-x-2">
                        <Users className="w-4 h-4" />
                        <span>{caseItem.visitors}</span>
                      </div>
                    </div>
                  </div>
                </motion.div>
              ))}
            </AnimatePresence>
          </motion.div>

          {filteredCases.length === 0 && (
            <div className="text-center py-12">
              <p className="text-gray-500 text-lg">没有找到匹配的案例</p>
            </div>
          )}
        </div>
      </section>

      {/* 案例详情弹窗 */}
      <AnimatePresence>
        {selectedCase && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            onClick={closeCaseDetail}
          >
            <motion.div
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.9 }}
              className="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto"
              onClick={(e) => e.stopPropagation()}
            >
              {/* 关闭按钮 */}
              <div className="sticky top-0 bg-white z-10 flex justify-end p-4 border-b">
                <button
                  onClick={closeCaseDetail}
                  className="p-2 hover:bg-gray-100 rounded-full transition-colors"
                >
                  <X className="w-6 h-6 text-gray-600" />
                </button>
              </div>

              <div className="p-8">
                {/* 案例头部信息 */}
                <div className="mb-8">
                  <div className="flex items-center justify-between mb-4">
                    <span className="bg-blue-600 text-white px-3 py-1 rounded-full text-sm">
                      {selectedCase.category}
                    </span>
                    <div className="flex items-center space-x-4 text-sm text-gray-500">
                      <div className="flex items-center space-x-1">
                        <Calendar className="w-4 h-4" />
                        <span>{selectedCase.date}</span>
                      </div>
                      <div className="flex items-center space-x-1">
                        <MapPin className="w-4 h-4" />
                        <span>{selectedCase.location}</span>
                      </div>
                    </div>
                  </div>
                  
                  <h2 className="text-3xl font-bold text-gray-900 mb-2">{selectedCase.title}</h2>
                  <p className="text-xl text-blue-600 font-semibold mb-4">{selectedCase.client}</p>
                  <p className="text-gray-600 leading-relaxed">{selectedCase.description}</p>
                </div>

                {/* 项目信息 */}
                <div className="grid md:grid-cols-3 gap-6 mb-8">
                  <div className="bg-gray-50 rounded-lg p-4 text-center">
                    <div className="text-2xl font-bold text-blue-600 mb-1">{selectedCase.area}</div>
                    <div className="text-gray-600">展台面积</div>
                  </div>
                  <div className="bg-gray-50 rounded-lg p-4 text-center">
                    <div className="text-2xl font-bold text-orange-600 mb-1">{selectedCase.visitors}</div>
                    <div className="text-gray-600">参观人数</div>
                  </div>
                  <div className="bg-gray-50 rounded-lg p-4 text-center">
                    <div className="text-2xl font-bold text-green-600 mb-1">{selectedCase.date}</div>
                    <div className="text-gray-600">项目时间</div>
                  </div>
                </div>

                {/* 项目特色 */}
                <div className="mb-8">
                  <h3 className="text-xl font-bold text-gray-900 mb-4">项目特色</h3>
                  <div className="grid md:grid-cols-2 gap-3">
                    {selectedCase.features.map((feature: string, index: number) => (
                      <div key={index} className="flex items-center space-x-2">
                        <div className="w-2 h-2 bg-blue-600 rounded-full"></div>
                        <span className="text-gray-600">{feature}</span>
                      </div>
                    ))}
                  </div>
                </div>

                {/* 挑战与解决方案 */}
                <div className="grid md:grid-cols-2 gap-8 mb-8">
                  <div>
                    <h3 className="text-xl font-bold text-gray-900 mb-4">项目挑战</h3>
                    <p className="text-gray-600 leading-relaxed">{selectedCase.challenges}</p>
                  </div>
                  <div>
                    <h3 className="text-xl font-bold text-gray-900 mb-4">解决方案</h3>
                    <p className="text-gray-600 leading-relaxed">{selectedCase.solution}</p>
                  </div>
                </div>

                {/* 项目图片 */}
                <div className="mb-8">
                  <h3 className="text-xl font-bold text-gray-900 mb-4">项目图片</h3>
                  <div className="grid md:grid-cols-3 gap-4">
                    {selectedCase.gallery.map((image: string, index: number) => (
                      <img 
                        key={index}
                        src={image} 
                        alt={`${selectedCase.title} ${index + 1}`}
                        className="w-full h-32 object-cover rounded-lg"
                      />
                    ))}
                  </div>
                </div>
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  )
}

export default Cases

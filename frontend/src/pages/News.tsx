import React, { useState } from 'react'
import { motion } from 'framer-motion'
import { 
  Calendar,
  User,
  Tag,
  ArrowRight,
  Search,
  Clock,
  TrendingUp
} from 'lucide-react'

const News: React.FC = () => {
  const [selectedCategory, setSelectedCategory] = useState('全部')
  const [searchTerm, setSearchTerm] = useState('')

  // 新闻分类
  const categories = ['全部', '公司新闻', '行业资讯', '展会预告', '技术分享']

  // 新闻数据
  const news = [
    {
      id: 1,
      title: '阔文展览成功承办2024上海科技创新展',
      category: '公司新闻',
      date: '2024-05-15',
      author: '阔文展览',
      image: '/images/tech-display-1.webp',
      summary: '近日，我公司成功承办了2024上海科技创新展的多个重要展台项目，为参展企业提供了专业的展台设计搭建服务，获得了客户的一致好评。',
      content: `2024年5月，上海阔文展览展示服务有限公司成功承办了2024上海科技创新展的多个重要展台项目。本次展会汇聚了众多科技企业，展示了最新的科技成果和创新产品。

我们为华为、腾讯、阿里巴巴等知名企业设计搭建了展台，每个展台都体现了企业的品牌特色和产品优势。在设计过程中，我们充分考虑了观众的参观体验，通过合理的布局和创新的展示方式，让每个展台都成为展会的亮点。

特别值得一提的是，我们为华为设计的科技创新展台采用了最新的AR/VR技术，让观众能够沉浸式地体验华为的最新产品。这一创新设计不仅吸引了大量观众，也为华为带来了丰厚的商业价值。

展会期间，我们的专业团队全程提供现场支持，确保每个展台的正常运行。通过这次成功的合作，我们进一步巩固了在科技展览领域的领先地位。`,
      readTime: '3分钟',
      views: 1256
    },
    {
      id: 2,
      title: '2024年展览行业发展趋势分析',
      category: '行业资讯',
      date: '2024-05-10',
      author: '行业分析师',
      image: '/images/exhibition-hall-1.jpg',
      summary: '随着数字化技术的快速发展，展览行业正在经历深刻的变革。本文深入分析了2024年展览行业的主要发展趋势和机遇。',
      content: `2024年，展览行业正在经历一场深刻的数字化变革。以下是我们观察到的主要发展趋势：

**1. 数字化展示技术普及**
AR/VR技术、全息投影、互动屏幕等数字化展示技术在展览中的应用越来越广泛。这些技术不仅提升了观众的参观体验，也为展商提供了更多的展示可能性。

**2. 绿色环保理念深入人心**
可持续发展和环保理念在展览行业中得到越来越多的重视。可重复使用的展台材料、节能环保的设计方案成为行业新标准。

**3. 线上线下融合发展**
混合式展览模式成为新趋势，线上虚拟展厅与线下实体展台相结合，为参展商提供更广泛的展示平台。

**4. 个性化定制需求增长**
企业对展台设计的个性化需求不断增长，标准化的展台已经无法满足企业的差异化需求。

**5. 数据驱动的展览效果评估**
通过大数据分析和人工智能技术，展览效果的评估变得更加精准和科学。

这些趋势为展览服务商带来了新的机遇和挑战，只有不断创新和适应变化，才能在激烈的市场竞争中立于不败之地。`,
      readTime: '5分钟',
      views: 892
    },
    {
      id: 3,
      title: '绿色环保展台设计理念的实践与思考',
      category: '技术分享',
      date: '2024-05-05',
      author: '设计总监',
      image: '/images/creative-display-1.jpg',
      summary: '在可持续发展成为全球共识的今天，如何在展台设计中融入绿色环保理念，实现美观与环保的完美结合，是我们一直在探索的课题。',
      content: `随着全球环保意识的提升，绿色环保已经成为展览行业的重要发展方向。作为专业的展台设计团队，我们一直致力于探索和实践绿色环保的设计理念。

**设计理念的转变**
传统的展台设计往往追求视觉冲击力，而忽略了环保因素。我们提出了"美观与环保并重"的设计理念，在保证展台美观度的同时，最大限度地减少对环境的影响。

**材料选择的创新**
我们优先选择可回收、可重复使用的材料，如竹材、回收铝材、环保板材等。这些材料不仅环保，而且具有良好的视觉效果。

**模块化设计方案**
通过模块化设计，展台可以灵活拆装和重新组合，大大提高了材料的利用率，减少了浪费。

**节能技术应用**
采用LED照明、智能控制系统等节能技术，有效降低展台的能源消耗。

**实践案例分享**
在最近的一个项目中，我们为某环保企业设计的展台完全采用了可回收材料，展会结束后，90%的材料得到了重复利用。这个项目不仅获得了客户的高度认可，也为行业树立了绿色展台设计的标杆。

绿色环保展台设计不仅是我们的社会责任，也是行业发展的必然趋势。我们将继续在这个方向上探索和创新，为客户提供更加环保、可持续的展台解决方案。`,
      readTime: '4分钟',
      views: 654
    },
    {
      id: 4,
      title: '第127届广交会即将开幕，展台搭建服务火热预约中',
      category: '展会预告',
      date: '2024-04-20',
      author: '阔文展览',
      image: '/images/booth-construction-1.jpg',
      summary: '第127届中国进出口商品交易会即将于4月开幕，我公司展台搭建服务已进入紧张的筹备阶段，欢迎有需求的企业提前预约。',
      content: `第127届中国进出口商品交易会（广交会）即将于4月15日在广州隆重开幕。作为中国历史最长、规模最大的综合性国际贸易盛会，广交会一直是企业展示形象、开拓市场的重要平台。

**展会基本信息**
- 展会时间：2024年4月15日-19日
- 展会地点：中国进出口商品交易会展馆
- 展会规模：预计参展企业超过25000家
- 预期观众：来自200多个国家和地区的采购商

**我们的服务优势**
阔文展览作为专业的展台设计搭建服务商，为广交会提供以下专业服务：

1. **一站式服务**：从设计方案到现场搭建的全流程服务
2. **经验丰富**：连续5年为广交会提供展台搭建服务
3. **专业团队**：拥有50+专业设计师和施工人员
4. **质量保证**：严格的质量控制体系，确保项目按时按质完成

**特色服务项目**
- 标准展位装修
- 特装展台设计搭建
- 展具租赁服务
- 现场技术支持
- 展后拆除服务

**预约须知**
由于广交会期间服务需求量大，建议有需求的企业提前1个月预约我们的服务。我们将根据客户需求提供个性化的设计方案和报价。

联系我们，让您的展台在广交会上脱颖而出！`,
      readTime: '3分钟',
      views: 1089
    },
    {
      id: 5,
      title: '阔文展览荣获"2023年度优秀展览服务商"称号',
      category: '公司新闻',
      date: '2024-04-10',
      author: '阔文展览',
      image: '/images/team-meeting-1.jpg',
      summary: '在近日举行的上海展览行业协会年度表彰大会上，阔文展览凭借优质的服务和良好的口碑，荣获"2023年度优秀展览服务商"称号。',
      content: `4月10日，上海展览行业协会2023年度表彰大会在上海国际会议中心隆重举行。在本次大会上，上海阔文展览展示服务有限公司凭借在2023年度的优异表现，荣获"2023年度优秀展览服务商"称号。

**获奖理由**
评审委员会认为，阔文展览在2023年度表现出色，主要体现在以下几个方面：

1. **服务质量优异**：客户满意度达到99%以上
2. **创新能力突出**：在展台设计中融入了多项创新技术
3. **项目执行力强**：全年完成项目100+个，零投诉记录
4. **行业贡献显著**：积极参与行业标准制定和人才培养

**2023年度成绩回顾**
- 服务客户数量：120+家
- 完成项目总数：150+个
- 展台总面积：50000+平方米
- 客户续约率：85%以上

**公司发展历程**
自2014年成立以来，阔文展览始终坚持"专业、用心、创新、合作"的企业理念，不断提升服务质量和专业水平。经过10年的发展，公司已经成为上海地区知名的展览服务商。

**未来发展规划**
公司总经理表示："这个奖项既是对我们过去工作的肯定，也是对未来发展的鞭策。我们将继续秉承专业精神，不断创新服务模式，为客户提供更加优质的展览服务。"

2024年，阔文展览将继续深耕展览服务领域，计划在数字化展示技术、绿色环保设计等方面加大投入，为客户提供更加创新、环保的展览解决方案。`,
      readTime: '4分钟',
      views: 743
    },
    {
      id: 6,
      title: '展台设计中的色彩心理学应用',
      category: '技术分享',
      date: '2024-04-01',
      author: '首席设计师',
      image: '/images/luxury-booth-1.jpg',
      summary: '色彩在展台设计中起着至关重要的作用，不同的色彩搭配会给观众带来不同的心理感受。本文探讨色彩心理学在展台设计中的实际应用。',
      content: `色彩是展台设计中最直观、最具感染力的视觉元素之一。合理运用色彩心理学原理，能够有效提升展台的吸引力和展示效果。

**色彩的心理效应**

**红色系**
- 心理效应：激情、活力、紧迫感
- 适用行业：食品、娱乐、体育用品
- 设计建议：作为点缀色使用，避免大面积应用

**蓝色系**
- 心理效应：专业、可信、稳重
- 适用行业：科技、金融、医疗
- 设计建议：可作为主色调，营造专业氛围

**绿色系**
- 心理效应：自然、健康、环保
- 适用行业：环保、农业、健康产品
- 设计建议：结合自然元素，强化环保理念

**橙色系**
- 心理效应：友好、温暖、创新
- 适用行业：教育、创意、服务业
- 设计建议：可用于营造亲和力，增强互动性

**实际应用案例**

**案例一：科技企业展台**
某科技公司的展台主要采用蓝白配色方案，辅以少量橙色点缀。蓝色营造了专业可信的氛围，白色增强了科技感，橙色点缀则增加了创新活力。

**案例二：环保企业展台**
某环保企业的展台采用绿色系为主色调，配合原木色和白色。这种配色方案完美诠释了企业的环保理念，给观众留下了深刻印象。

**设计要点总结**
1. 根据企业属性选择主色调
2. 考虑目标观众的文化背景
3. 注意色彩搭配的和谐性
4. 适度使用对比色增强视觉冲击力
5. 控制色彩数量，避免过于复杂

通过科学运用色彩心理学原理，我们能够设计出更具感染力和说服力的展台，帮助客户在激烈的展会竞争中脱颖而出。`,
      readTime: '6分钟',
      views: 567
    }
  ]

  // 过滤新闻
  const filteredNews = news.filter(item => {
    const matchCategory = selectedCategory === '全部' || item.category === selectedCategory
    const matchSearch = item.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                       item.summary.toLowerCase().includes(searchTerm.toLowerCase())
    return matchCategory && matchSearch
  })

  // 热门新闻（按浏览量排序）
  const hotNews = [...news].sort((a, b) => b.views - a.views).slice(0, 5)

  return (
    <div className="bg-white">
      {/* 页面头部 */}
      <section className="relative bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
        <div 
          className="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20"
          style={{ backgroundImage: 'url(/images/exhibition-visitors-1.webp)' }}
        ></div>
        <div className="relative container mx-auto px-4 text-center">
          <motion.h1 
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            className="text-4xl lg:text-5xl font-bold mb-6"
          >
            新闻动态
          </motion.h1>
          <motion.p 
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.2 }}
            className="text-xl text-blue-100 max-w-3xl mx-auto"
          >
            关注行业动态，分享专业见解，与您一起探索展览行业的发展趋势
          </motion.p>
        </div>
      </section>

      <section className="py-20">
        <div className="container mx-auto px-4">
          <div className="grid lg:grid-cols-3 gap-12">
            {/* 主要内容区 */}
            <div className="lg:col-span-2">
              {/* 筛选和搜索 */}
              <div className="mb-8">
                <div className="flex flex-col sm:flex-row gap-4 items-center justify-between mb-6">
                  {/* 分类筛选 */}
                  <div className="flex flex-wrap gap-2">
                    {categories.map((category) => (
                      <button
                        key={category}
                        onClick={() => setSelectedCategory(category)}
                        className={`px-4 py-2 rounded-lg font-medium transition-colors duration-200 ${
                          selectedCategory === category
                            ? 'bg-blue-600 text-white'
                            : 'bg-gray-100 text-gray-600 hover:bg-blue-50'
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
                      placeholder="搜索新闻..."
                      value={searchTerm}
                      onChange={(e) => setSearchTerm(e.target.value)}
                      className="pl-10 pr-4 py-2 w-64 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                </div>
              </div>

              {/* 新闻列表 */}
              <div className="space-y-8">
                {filteredNews.map((item, index) => (
                  <motion.article
                    key={item.id}
                    initial={{ opacity: 0, y: 30 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.6, delay: index * 0.1 }}
                    viewport={{ once: true }}
                    className="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden"
                  >
                    <div className="md:flex">
                      <div className="md:w-1/3">
                        <img 
                          src={item.image} 
                          alt={item.title}
                          className="w-full h-48 md:h-full object-cover"
                        />
                      </div>
                      <div className="md:w-2/3 p-6">
                        <div className="flex items-center justify-between mb-3">
                          <span className="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm font-medium">
                            {item.category}
                          </span>
                          <div className="flex items-center text-sm text-gray-500 space-x-4">
                            <div className="flex items-center space-x-1">
                              <Calendar className="w-4 h-4" />
                              <span>{item.date}</span>
                            </div>
                            <div className="flex items-center space-x-1">
                              <Clock className="w-4 h-4" />
                              <span>{item.readTime}</span>
                            </div>
                          </div>
                        </div>
                        
                        <h2 className="text-xl font-bold text-gray-900 mb-3 hover:text-blue-600 transition-colors">
                          {item.title}
                        </h2>
                        
                        <p className="text-gray-600 mb-4 line-clamp-3">
                          {item.summary}
                        </p>
                        
                        <div className="flex items-center justify-between">
                          <div className="flex items-center space-x-4 text-sm text-gray-500">
                            <div className="flex items-center space-x-1">
                              <User className="w-4 h-4" />
                              <span>{item.author}</span>
                            </div>
                            <div className="flex items-center space-x-1">
                              <TrendingUp className="w-4 h-4" />
                              <span>{item.views} 浏览</span>
                            </div>
                          </div>
                          
                          <button className="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium transition-colors">
                            阅读更多 <ArrowRight className="ml-1 w-4 h-4" />
                          </button>
                        </div>
                      </div>
                    </div>
                  </motion.article>
                ))}
              </div>

              {filteredNews.length === 0 && (
                <div className="text-center py-12">
                  <p className="text-gray-500 text-lg">没有找到匹配的新闻</p>
                </div>
              )}
            </div>

            {/* 侧边栏 */}
            <div className="lg:col-span-1">
              <div className="space-y-8">
                {/* 热门新闻 */}
                <motion.div
                  initial={{ opacity: 0, x: 30 }}
                  whileInView={{ opacity: 1, x: 0 }}
                  transition={{ duration: 0.6 }}
                  viewport={{ once: true }}
                  className="bg-white rounded-xl shadow-lg p-6"
                >
                  <h3 className="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <TrendingUp className="w-5 h-5 text-orange-500 mr-2" />
                    热门新闻
                  </h3>
                  <div className="space-y-4">
                    {hotNews.map((item, index) => (
                      <div key={item.id} className="flex items-start space-x-3 group cursor-pointer">
                        <span className="flex-shrink-0 w-6 h-6 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-sm font-bold">
                          {index + 1}
                        </span>
                        <div className="flex-1">
                          <h4 className="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2">
                            {item.title}
                          </h4>
                          <div className="flex items-center text-xs text-gray-500 mt-1 space-x-2">
                            <span>{item.date}</span>
                            <span>•</span>
                            <span>{item.views} 浏览</span>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                </motion.div>

                {/* 分类标签 */}
                <motion.div
                  initial={{ opacity: 0, x: 30 }}
                  whileInView={{ opacity: 1, x: 0 }}
                  transition={{ duration: 0.6, delay: 0.2 }}
                  viewport={{ once: true }}
                  className="bg-white rounded-xl shadow-lg p-6"
                >
                  <h3 className="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <Tag className="w-5 h-5 text-blue-500 mr-2" />
                    分类标签
                  </h3>
                  <div className="flex flex-wrap gap-2">
                    {categories.filter(cat => cat !== '全部').map((category) => {
                      const count = news.filter(item => item.category === category).length
                      return (
                        <button
                          key={category}
                          onClick={() => setSelectedCategory(category)}
                          className={`px-3 py-1 rounded-full text-sm transition-colors ${
                            selectedCategory === category
                              ? 'bg-blue-600 text-white'
                              : 'bg-gray-100 text-gray-600 hover:bg-blue-50'
                          }`}
                        >
                          {category} ({count})
                        </button>
                      )
                    })}
                  </div>
                </motion.div>

                {/* 联系我们 */}
                <motion.div
                  initial={{ opacity: 0, x: 30 }}
                  whileInView={{ opacity: 1, x: 0 }}
                  transition={{ duration: 0.6, delay: 0.4 }}
                  viewport={{ once: true }}
                  className="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl p-6 text-white"
                >
                  <h3 className="text-xl font-bold mb-4">需要展览服务？</h3>
                  <p className="text-blue-100 mb-4">
                    联系我们获取专业的展台设计方案和报价
                  </p>
                  <a 
                    href="/contact"
                    className="inline-flex items-center bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors duration-200"
                  >
                    立即咨询 <ArrowRight className="ml-2 w-4 h-4" />
                  </a>
                </motion.div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  )
}

export default News

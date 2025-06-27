import React from 'react'
import { Link } from 'react-router-dom'
import { motion } from 'framer-motion'
import { 
  Lightbulb, 
  Wrench, 
  Users, 
  Award,
  ArrowRight,
  CheckCircle,
  Star,
  MapPin,
  Phone,
  Mail
} from 'lucide-react'

const Home: React.FC = () => {
  // 核心服务数据
  const services = [
    {
      icon: Lightbulb,
      title: '展台设计',
      description: '创新的设计理念，专业的设计团队，为您打造独特的展台形象',
      features: ['创意设计', '3D效果图', '专业方案']
    },
    {
      icon: Wrench,
      title: '展台搭建',
      description: '专业的搭建团队，优质的材料选择，确保展台完美呈现',
      features: ['专业施工', '优质材料', '按时交付']
    },
    {
      icon: Users,
      title: '展会策划',
      description: '全方位的展会策划服务，从前期规划到现场执行一站式解决',
      features: ['活动策划', '现场管理', '效果评估']
    },
    {
      icon: Award,
      title: '设备租赁',
      description: '丰富的设备资源，专业的技术支持，为您的展会提供保障',
      features: ['设备齐全', '技术支持', '性价比高']
    }
  ]

  // 精选案例数据
  const featuredCases = [
    {
      id: 1,
      title: '科技创新展台',
      category: '科技展',
      image: '/images/tech-display-1.webp',
      description: '为知名科技企业打造的现代化展台，融合科技感与创新理念'
    },
    {
      id: 2,
      title: '豪华汽车展台',
      category: '汽车展',
      image: '/images/luxury-booth-1.jpg',
      description: '高端汽车品牌展台设计，彰显品牌奢华与精致工艺'
    },
    {
      id: 3,
      title: '创意设计展示',
      category: '设计展',
      image: '/images/creative-display-1.jpg',
      description: '独特的创意设计展台，充分展现设计师的创意理念'
    }
  ]

  // 公司优势数据
  const advantages = [
    { number: '500+', text: '成功案例' },
    { number: '10+', text: '服务年限' },
    { number: '50+', text: '专业团队' },
    { number: '99%', text: '客户满意度' }
  ]

  return (
    <div className="bg-white">
      {/* 英雄区域 */}
      <section className="relative bg-gradient-to-r from-blue-600 to-blue-800 text-white overflow-hidden">
        {/* 背景装饰 */}
        <div className="absolute inset-0 bg-black bg-opacity-20"></div>
        <div 
          className="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-30"
          style={{ backgroundImage: 'url(/images/exhibition-hall-1.jpg)' }}
        ></div>
        
        <div className="relative container mx-auto px-4 py-20 lg:py-32">
          <div className="max-w-4xl">
            <motion.h1 
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8 }}
              className="text-4xl lg:text-6xl font-bold mb-6 leading-tight"
            >
              专业展台设计搭建
              <span className="block text-orange-400">创造展会价值</span>
            </motion.h1>
            
            <motion.p 
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8, delay: 0.2 }}
              className="text-xl lg:text-2xl mb-8 text-blue-100"
            >
              上海阔文展览展示服务有限公司，致力于为企业提供一站式展览解决方案，
              以创新设计和专业服务助力企业展会成功。
            </motion.p>
            
            <motion.div 
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8, delay: 0.4 }}
              className="flex flex-col sm:flex-row gap-4"
            >
              <Link 
                to="/contact"
                className="inline-flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors duration-200"
              >
                免费咨询 <ArrowRight className="ml-2 w-5 h-5" />
              </Link>
              <Link 
                to="/cases"
                className="inline-flex items-center justify-center border-2 border-white text-white hover:bg-white hover:text-blue-600 px-8 py-4 rounded-lg text-lg font-semibold transition-colors duration-200"
              >
                查看案例
              </Link>
            </motion.div>
          </div>
        </div>
      </section>

      {/* 公司数据统计 */}
      <section className="py-16 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-8">
            {advantages.map((item, index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                viewport={{ once: true }}
                className="text-center"
              >
                <div className="text-4xl lg:text-5xl font-bold text-blue-600 mb-2">
                  {item.number}
                </div>
                <div className="text-gray-600 text-lg">{item.text}</div>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* 公司简介 */}
      <section className="py-20">
        <div className="container mx-auto px-4">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <motion.div
              initial={{ opacity: 0, x: -30 }}
              whileInView={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.8 }}
              viewport={{ once: true }}
            >
              <h2 className="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">
                关于阔文展览
              </h2>
              <p className="text-lg text-gray-600 mb-6 leading-relaxed">
                上海阔文展览展示服务有限公司成立于2014年，是一家专业从事展台设计搭建的综合性服务企业。
                我们拥有经验丰富的设计团队和专业的施工队伍，为客户提供从设计方案到现场搭建的一站式服务。
              </p>
              <p className="text-lg text-gray-600 mb-8 leading-relaxed">
                多年来，我们服务过众多知名企业，在科技、汽车、医疗、教育等多个行业积累了丰富的展览经验，
                赢得了客户的一致好评和信赖。
              </p>
              
              <div className="grid grid-cols-2 gap-6">
                <div className="flex items-start space-x-3">
                  <CheckCircle className="w-6 h-6 text-green-500 mt-1" />
                  <div>
                    <h4 className="font-semibold text-gray-900">专业团队</h4>
                    <p className="text-gray-600">资深设计师和施工团队</p>
                  </div>
                </div>
                <div className="flex items-start space-x-3">
                  <CheckCircle className="w-6 h-6 text-green-500 mt-1" />
                  <div>
                    <h4 className="font-semibold text-gray-900">一站式服务</h4>
                    <p className="text-gray-600">从设计到搭建全程服务</p>
                  </div>
                </div>
                <div className="flex items-start space-x-3">
                  <CheckCircle className="w-6 h-6 text-green-500 mt-1" />
                  <div>
                    <h4 className="font-semibold text-gray-900">丰富经验</h4>
                    <p className="text-gray-600">500+成功案例积累</p>
                  </div>
                </div>
                <div className="flex items-start space-x-3">
                  <CheckCircle className="w-6 h-6 text-green-500 mt-1" />
                  <div>
                    <h4 className="font-semibold text-gray-900">优质服务</h4>
                    <p className="text-gray-600">99%客户满意度保证</p>
                  </div>
                </div>
              </div>
            </motion.div>
            
            <motion.div
              initial={{ opacity: 0, x: 30 }}
              whileInView={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.8 }}
              viewport={{ once: true }}
              className="relative"
            >
              <img 
                src="/images/team-meeting-1.jpg" 
                alt="阔文展览团队"
                className="rounded-2xl shadow-2xl w-full"
              />
              <div className="absolute -bottom-6 -right-6 bg-orange-500 text-white p-6 rounded-xl shadow-lg">
                <div className="text-2xl font-bold">10+</div>
                <div className="text-sm">年服务经验</div>
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* 核心服务 */}
      <section className="py-20 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <motion.h2 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              viewport={{ once: true }}
              className="text-3xl lg:text-4xl font-bold text-gray-900 mb-4"
            >
              核心服务
            </motion.h2>
            <motion.p 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              viewport={{ once: true }}
              className="text-lg text-gray-600 max-w-2xl mx-auto"
            >
              专业的展览服务，为您的企业展示提供全方位支持
            </motion.p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            {services.map((service, index) => {
              const IconComponent = service.icon
              return (
                <motion.div
                  key={index}
                  initial={{ opacity: 0, y: 30 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.6, delay: index * 0.1 }}
                  viewport={{ once: true }}
                  className="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition-shadow duration-300"
                >
                  <div className="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                    <IconComponent className="w-8 h-8 text-blue-600" />
                  </div>
                  <h3 className="text-xl font-bold text-gray-900 mb-4">{service.title}</h3>
                  <p className="text-gray-600 mb-6">{service.description}</p>
                  <ul className="space-y-2">
                    {service.features.map((feature, featureIndex) => (
                      <li key={featureIndex} className="flex items-center text-sm text-gray-600">
                        <Star className="w-4 h-4 text-orange-500 mr-2" />
                        {feature}
                      </li>
                    ))}
                  </ul>
                </motion.div>
              )
            })}
          </div>

          <div className="text-center mt-12">
            <Link 
              to="/services"
              className="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors duration-200"
            >
              了解更多服务 <ArrowRight className="ml-2 w-5 h-5" />
            </Link>
          </div>
        </div>
      </section>

      {/* 精选案例 */}
      <section className="py-20">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <motion.h2 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              viewport={{ once: true }}
              className="text-3xl lg:text-4xl font-bold text-gray-900 mb-4"
            >
              精选案例
            </motion.h2>
            <motion.p 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              viewport={{ once: true }}
              className="text-lg text-gray-600 max-w-2xl mx-auto"
            >
              展示我们的专业能力和创意成果
            </motion.p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {featuredCases.map((caseItem, index) => (
              <motion.div
                key={caseItem.id}
                initial={{ opacity: 0, y: 30 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                viewport={{ once: true }}
                className="group bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300"
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
                </div>
                <div className="p-6">
                  <h3 className="text-xl font-bold text-gray-900 mb-2">{caseItem.title}</h3>
                  <p className="text-gray-600">{caseItem.description}</p>
                </div>
              </motion.div>
            ))}
          </div>

          <div className="text-center mt-12">
            <Link 
              to="/cases"
              className="inline-flex items-center bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors duration-200"
            >
              查看更多案例 <ArrowRight className="ml-2 w-5 h-5" />
            </Link>
          </div>
        </div>
      </section>

      {/* 联系我们 CTA */}
      <section className="py-20 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
        <div className="container mx-auto px-4 text-center">
          <motion.h2 
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            viewport={{ once: true }}
            className="text-3xl lg:text-4xl font-bold mb-6"
          >
            准备开始您的展览项目？
          </motion.h2>
          <motion.p 
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.2 }}
            viewport={{ once: true }}
            className="text-xl text-blue-100 mb-8"
          >
            联系我们获取专业的展览设计方案和报价
          </motion.p>
          
          <motion.div 
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.4 }}
            viewport={{ once: true }}
            className="flex flex-col sm:flex-row gap-6 justify-center items-center"
          >
            <div className="flex items-center space-x-2">
              <Phone className="w-6 h-6" />
              <span className="text-lg">021-12345678</span>
            </div>
            <div className="flex items-center space-x-2">
              <Mail className="w-6 h-6" />
              <span className="text-lg">info@kuowen-exhibition.com</span>
            </div>
            <Link 
              to="/contact"
              className="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-lg font-semibold transition-colors duration-200"
            >
              立即咨询
            </Link>
          </motion.div>
        </div>
      </section>
    </div>
  )
}

export default Home
